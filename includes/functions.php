<?php
require_once 'config/db.php';

function sudahMasuk() {
    return isset($_SESSION['user_id']);
}

function tanganiDaftar($data) {
    global $conn;
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $email = mysqli_real_escape_string($conn, $data['email']);
    $sandi = password_hash($data['sandi'], PASSWORD_DEFAULT);
    $query = "INSERT INTO pengguna (nama, email, sandi, role) VALUES ('$nama', '$email', '$sandi', 'pengguna')";
    if (mysqli_query($conn, $query)) {
        // Kirim email verifikasi (PHPMailer diperlukan)
        header('Location: ?page=masuk');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

function tanganiMasuk($data) {
    global $conn;
    $email = mysqli_real_escape_string($conn, $data['email']);
    $sandi = $data['sandi'];
    $query = "SELECT * FROM pengguna WHERE email='$email'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($sandi, $row['sandi'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            header('Location: ?page=beranda');
            exit;
        }
    }
    echo "Email atau kata sandi salah.";
}

function tanganiResetSandi($data) {
    // Implementasi PHPMailer untuk mengirim tautan reset sandi
    echo "Tautan reset kata sandi telah dikirim ke email Anda.";
}

function tambahKeKeranjang($data) {
    global $conn;
    $user_id = $_SESSION['user_id'];
    $tanaman_id = mysqli_real_escape_string($conn, $data['tanaman_id']);
    $jumlah = mysqli_real_escape_string($conn, $data['jumlah']);
    $query = "INSERT INTO keranjang (user_id, tanaman_id, jumlah) VALUES ('$user_id', '$tanaman_id', '$jumlah')";
    mysqli_query($conn, $query);
}

function updateKeranjang($data) {
    global $conn;
    $user_id = $_SESSION['user_id'];
    foreach ($data['jumlah'] as $id => $jumlah) {
        $jumlah = mysqli_real_escape_string($conn, $jumlah);
        if ($jumlah == 0) {
            $query = "DELETE FROM keranjang WHERE id='$id' AND user_id='$user_id'";
        } else {
            $query = "UPDATE keranjang SET jumlah='$jumlah' WHERE id='$id' AND user_id='$user_id'";
        }
        mysqli_query($conn, $query);
    }
}
function buatPesanan($data) {
    global $conn;
    $user_id = $_SESSION['user_id'];

    // [1] Hitung total keranjang (tetap sama)
    $total_query = "SELECT SUM(t.harga * k.jumlah) as total 
                    FROM keranjang k 
                    JOIN tanaman t ON k.tanaman_id = t.id 
                    WHERE k.user_id = '$user_id'";
    $total_result = mysqli_query($conn, $total_query);
    $total = mysqli_fetch_assoc($total_result)['total'] ?? 0;

    if ($total <= 0) {
        $_SESSION['error'] = "Total harga tidak valid.";
        header("Location: ?page=keranjang");
        exit;
    }

    // [2] Debug: Tampilkan query INSERT sebelum eksekusi
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $alamat = mysqli_real_escape_string($conn, $data['alamat']);
    $metode_pengiriman = mysqli_real_escape_string($conn, $data['metode_pengiriman']);
    
    $query = "INSERT INTO pesanan (user_id, nama, alamat, metode_pengiriman, total_harga, status, tanggal_pesan) 
              VALUES ('$user_id', '$nama', '$alamat', '$metode_pengiriman', '$total', 'pending', NOW())";
    
    // [3] Eksekusi dengan error handling lebih ketat
    if (mysqli_query($conn, $query)) {
        $pesanan_id = mysqli_insert_id($conn);
        
        // [4] Debug: Pastikan ID valid
        if($pesanan_id <= 0) {
            die("Error: Gagal mendapatkan ID pesanan. Query: ".$query);
        }
        
        // Kosongkan keranjang
        mysqli_query($conn, "DELETE FROM keranjang WHERE user_id = '$user_id'");
        
        // [5] Redirect dengan ID
        header("Location: ?page=pembayaran&pesanan_id=".$pesanan_id);
        exit();
    } else {
        // [6] Tangkap error SQL dengan detail
        $_SESSION['error'] = "Gagal membuat pesanan: " . mysqli_error($conn);
        header("Location: ?page=keranjang");
        exit;
    }
}

function ambilKeranjang($user_id) {
    global $conn;
    $query = "SELECT k.id, k.tanaman_id, k.jumlah, t.harga, t.nama FROM keranjang k JOIN tanaman t ON k.tanaman_id = t.id WHERE k.user_id='$user_id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function unggahPostingan($data) {
    global $conn;
    $base_dir = dirname(dirname(__FILE__));
    $upload_dir = 'uploads/';
    $full_upload_dir = $base_dir . '/' . $upload_dir;

    if (!isset($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Permintaan tidak valid.";
        header("Location: ?page=komunitas");
        exit();
    }

    $konten = mysqli_real_escape_string($conn, $data['konten']);
    $user_id = $_SESSION['user_id'];
    $tipe = 'tulisan';
    $media_path = '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = uniqid('img_', true) . '.' . $file_ext;
            $target_path = $full_upload_dir . $new_file_name;
            $relative_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $target_path)) {
                if (file_exists($target_path)) {
                    $media_path = $relative_path;
                    $tipe = 'gambar';
                }
            } else {
                $_SESSION['error'] = "Gagal mengupload file: Error " . $_FILES['file']['error'];
                header("Location: ?page=komunitas");
                exit();
            }
        } else {
            $_SESSION['error'] = "Format file tidak didukung.";
            header("Location: ?page=komunitas");
            exit();
        }
    }

    $konten_db = $konten;
    if (!empty($media_path)) {
        $konten_db .= '|' . $media_path;
    }

    $check_query = "SELECT id FROM postingan_komunitas WHERE user_id = '$user_id' AND konten = '$konten_db' AND waktu > NOW() - INTERVAL 5 SECOND";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Postingan ganda terdeteksi, coba lagi.";
    } else {
        $query = "INSERT INTO postingan_komunitas (user_id, konten, tipe, waktu) VALUES ('$user_id', '$konten_db', '$tipe', NOW())";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Postingan berhasil ditambahkan!";
            unset($_SESSION['csrf_token']);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
    header("Location: ?page=komunitas", true, 303);
    exit();
}

function tambahKomentar($data) {
    global $conn;
    $postingan_id = mysqli_real_escape_string($conn, $data['postingan_id']);
    $komentar = mysqli_real_escape_string($conn, $data['komentar']);
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO komentar (postingan_id, user_id, komentar, waktu) VALUES ('$postingan_id', '$user_id', '$komentar', NOW())";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Komentar berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    header("Location: ?page=komunitas");
    exit();
}
function editPostingan($data) {
    global $conn;
    $postingan_id = intval($data['postingan_id']);
    $konten = mysqli_real_escape_string($conn, $data['konten']);
    $user_id = $_SESSION['user_id'];
    $new_media_path = '';

    // Debugging sementara
    // echo "User ID: " . $user_id . "<br>";
    // exit;

    // Periksa postingan asli untuk mempertahankan gambar lama
    $query_check = "SELECT user_id, konten, tipe FROM postingan_komunitas WHERE id = '$postingan_id'";
    $result = mysqli_query($conn, $query_check);
    $post = mysqli_fetch_assoc($result);

    if ($post) {
        if ($post['user_id'] == $user_id || cekRoleAdmin()) {
            $content_parts = explode('|', $post['konten']);
            $old_text = $content_parts[0];
            $old_media_path = isset($content_parts[1]) ? $content_parts[1] : '';

            // Handle unggahan file baru jika ada
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $file_name = $_FILES['file']['name'];
                $file_tmp = $_FILES['file']['tmp_name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($file_ext, $allowed_ext)) {
                    $base_dir = dirname(dirname(__FILE__));
                    $upload_dir = 'uploads/';
                    $full_upload_dir = $base_dir . '/' . $upload_dir;
                    $new_file_name = uniqid('img_', true) . '.' . $file_ext;
                    $target_path = $full_upload_dir . $new_file_name;
                    $relative_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $target_path)) {
                        if (file_exists($target_path)) {
                            $new_media_path = $relative_path;
                        }
                    }
                }
            }

            // Siapkan konten baru
            $new_content = $konten;
            if (!empty($new_media_path)) {
                $new_content .= '|' . $new_media_path;
            } elseif (!empty($old_media_path)) {
                $new_content .= '|' . $old_media_path; // Pertahankan gambar lama jika tidak ada unggahan baru
            }

            $update_query = "UPDATE postingan_komunitas SET konten = '$new_content' WHERE id = '$postingan_id'";
            if (mysqli_query($conn, $update_query)) {
                $_SESSION['success'] = "Postingan berhasil diperbarui!";
            } else {
                $_SESSION['error'] = "Error saat memperbarui: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['error'] = "Anda tidak memiliki izin untuk mengedit postingan ini. User ID: $user_id, Post User ID: " . $post['user_id'];
        }
    } else {
        $_SESSION['error'] = "Postingan tidak ditemukan.";
    }
    header("Location: ?page=komunitas");
    exit();
}

function hapusPostingan($postingan_id) {
    global $conn;
    $user_id = $_SESSION['user_id'];

    $query_check = "SELECT user_id FROM postingan_komunitas WHERE id = '$postingan_id'";
    $result = mysqli_query($conn, $query_check);
    $post = mysqli_fetch_assoc($result);

    if ($post && ($post['user_id'] == $user_id || cekRoleAdmin())) {
        $delete_query = "DELETE FROM postingan_komunitas WHERE id = '$postingan_id'";
        if (mysqli_query($conn, $delete_query)) {
            $_SESSION['success'] = "Postingan berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = "Anda tidak memiliki izin untuk menghapus postingan ini.";
    }
    header("Location: ?page=komunitas");
    exit();
}


function updateProfil($data) {
    global $conn;
    $user_id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $alamat = mysqli_real_escape_string($conn, $data['alamat'] ?? '');
    $bio = mysqli_real_escape_string($conn, $data['bio'] ?? '');
    $no_telepon = mysqli_real_escape_string($conn, $data['no_telepon'] ?? '');
    $query = "UPDATE pengguna SET nama='$nama', alamat='$alamat', bio='$bio', no_telepon='$no_telepon' WHERE id='$user_id'";
    mysqli_query($conn, $query);
}

function tambahTanaman($data) {
    global $conn;
    $nama = mysqli_real_escape_string($conn, $data['nama']);
    $harga = mysqli_real_escape_string($conn, $data['harga']);
    $cara_menanam = mysqli_real_escape_string($conn, $data['cara_menanam']);
    $saran_tempat = mysqli_real_escape_string($conn, $data['saran_tempat']);
    $suhu = mysqli_real_escape_string($conn, $data['suhu']);
    $kelembapan = mysqli_real_escape_string($conn, $data['kelembapan']);
    $stok = mysqli_real_escape_string($conn, $data['stok']);
    $query = "INSERT INTO tanaman (nama, harga, cara_menanam, saran_tempat, suhu, kelembapan, stok) VALUES ('$nama', '$harga', '$cara_menanam', '$saran_tempat', '$suhu', '$kelembapan', '$stok')";
    mysqli_query($conn, $query);
}

function updateStatusPesanan($data) {
    global $conn;
    $pesanan_id = mysqli_real_escape_string($conn, $data['pesanan_id']); // Ubah dari 'id' ke 'pesanan_id'
    $status = mysqli_real_escape_string($conn, $data['status']);
    
    $query = "UPDATE pesanan SET status='$status' WHERE pesanan_id='$pesanan_id'"; // Ubah 'id' ke 'pesanan_id'
    mysqli_query($conn, $query);
}

function cekRoleAdmin() {
    if (!isset($_SESSION['user_id'])) return false;
    
    global $conn;
    $user_id = $_SESSION['user_id'];
    $query = "SELECT role FROM pengguna WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        return $user['role'] === 'admin';
    }
    return false;
}
function keluar() {
    session_destroy();
}
?>