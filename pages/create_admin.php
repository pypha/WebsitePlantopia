<?php
// 1. Koneksi database
require_once 'config/db.php';
if (!$conn) {
    die("<h1>Koneksi database gagal: " . mysqli_connect_error() . "</h1>");
}

// 2. Data admin
$data_admin = [
    'nama' => 'Super Admin',
    'email' => 'admin@cli.com',
    'sandi' => 'rahasia123' . rand(1000, 9999),
    'role' => 'admin'
];

try {
    // 3. Validasi data
    if (!filter_var($data_admin['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email tidak valid");
    }
    
    // 4. Hash password
    $options = ['cost' => 12];
    $sandi_terhash = password_hash($data_admin['sandi'], PASSWORD_BCRYPT, $options);
    if (!$sandi_terhash) throw new Exception("Gagal melakukan hashing password");
    
    // 5. Escape string
    $nama = mysqli_real_escape_string($conn, $data_admin['nama']);
    $email = mysqli_real_escape_string($conn, $data_admin['email']);
    $role = mysqli_real_escape_string($conn, $data_admin['role']);
    
    // 6. Query INSERT
    $query = "INSERT INTO pengguna (nama, email, sandi, role, created_at) 
              VALUES ('$nama', '$email', '$sandi_terhash', '$role', NOW())";
    
    // 7. Eksekusi query
    if (mysqli_query($conn, $query)) {
        $admin_id = mysqli_insert_id($conn);
        
        // 8. Output sukses
        echo "<style>body{font-family:Arial,sans-serif;line-height:1.6}</style>";
        echo "<div style='max-width:800px;margin:0 auto;padding:20px;border:1px solid #ddd;border-radius:5px'>";
        echo "<h1 style='color:green'>Admin berhasil dibuat!</h1>";
        echo "<div style='background:#f8f8f8;padding:15px;margin:15px 0;border-left:4px solid green'>";
        echo "<p><strong>ID Admin:</strong> $admin_id</p>";
        echo "<p><strong>Nama:</strong> " . htmlspecialchars($data_admin['nama']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($data_admin['email']) . "</p>";
        echo "<p><strong>Password:</strong> " . htmlspecialchars($data_admin['sandi']) . "</p>";
        echo "</div>";
        
        echo "<div style='background:#fff3cd;padding:15px;border-left:4px solid #ffc107'>";
        echo "<h3 style='color:#856404'>Penting!</h3>";
        echo "<ol>";
        echo "<li>Segera ganti password setelah login pertama</li>";
        echo "<li>Hapus file ini dari server</li>";
        echo "<li>Tambahkan verifikasi email jika diperlukan</li>";
        echo "</ol>";
        echo "</div>";
        
        // 9. Auto-delete script (opsional)
        echo "<script>
              if (confirm('Hapus file script ini sekarang?')) {
                  fetch('?delete_script=1').then(() => location.reload());
              }
              </script>";
        
        if (isset($_GET['delete_script'])) {
            unlink(__FILE__);
            exit;
        }
    } else {
        throw new Exception("Gagal mengeksekusi query: " . mysqli_error($conn));
    }
} catch (Exception $e) {
    echo "<div style='color:red;background:#ffebee;padding:20px;border-radius:5px'>";
    echo "<h1>Error: " . $e->getMessage() . "</h1>";
    
    // Debug info
    echo "<h3>Informasi Debug:</h3>";
    echo "<pre>Query: " . htmlspecialchars($query ?? '') . "</pre>";
    echo "<pre>Data: " . print_r($data_admin, true) . "</pre>";
    
    // Cek tabel
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'pengguna'");
    if (mysqli_num_rows($result) > 0) {
        echo "<p>Tabel 'pengguna' ada.</p>";
        $cols = mysqli_query($conn, "SHOW COLUMNS FROM pengguna");
        echo "<h4>Kolom tabel:</h4><ul>";
        while ($col = mysqli_fetch_assoc($cols)) {
            echo "<li>{$col['Field']} ({$col['Type']})</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>Tabel 'pengguna' tidak ditemukan!</p>";
    }
    echo "</div>";
}
?>