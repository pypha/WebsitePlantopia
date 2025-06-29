<?php
if (!cekRoleAdmin()) {
    header("Location: ?page=beranda");
    exit();
}

global $conn;

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token keamanan tidak valid";
        header("Location: ?page=admin_manajemen_tanaman");
        exit();
    }

    // Basic validation
    $required = ['nama', 'harga', 'stok', 'cara_menanam'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error'] = "Kolom $field harus diisi";
            header("Location: ?page=admin_manajemen_tanaman");
            exit();
        }
    }

    // Process data
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);
    $cara_menanam = mysqli_real_escape_string($conn, $_POST['cara_menanam']);
    $saran_tempat = mysqli_real_escape_string($conn, $_POST['saran_tempat'] ?? '');
    $suhu = mysqli_real_escape_string($conn, $_POST['suhu'] ?? '');
    $kelembapan = mysqli_real_escape_string($conn, $_POST['kelembapan'] ?? '');

    // Handle image upload
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $gambar = 'tanaman_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar);
        }
    }

    // Insert or Update
    if ($id > 0) {
        // Update existing record
        $sql = "UPDATE tanaman SET 
                nama = '$nama',
                harga = $harga,
                stok = $stok,
                cara_menanam = '$cara_menanam',
                saran_tempat = '$saran_tempat',
                suhu = '$suhu',
                kelembapan = '$kelembapan'";
        
        if (!empty($gambar)) {
            $sql .= ", gambar = '$gambar'";
        }
        
        $sql .= " WHERE id = $id";
    } else {
        // Insert new record
        if (empty($gambar)) {
            $_SESSION['error'] = "Gambar harus diupload";
            header("Location: ?page=admin_manajemen_tanaman");
            exit();
        }
        
        $sql = "INSERT INTO tanaman 
                (nama, harga, stok, cara_menanam, saran_tempat, suhu, kelembapan, gambar)
                VALUES 
                ('$nama', $harga, $stok, '$cara_menanam', '$saran_tempat', '$suhu', '$kelembapan', '$gambar')";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Data tanaman berhasil " . ($id > 0 ? "diperbarui" : "ditambahkan");
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }

    header("Location: ?page=admin_manajemen_tanaman");
    exit();
}

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'hapus' && isset($_GET['id'])) {
    if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token keamanan tidak valid";
        header("Location: ?page=admin_manajemen_tanaman");
        exit();
    }

    $id = intval($_GET['id']);
    mysqli_query($conn, "DELETE FROM tanaman WHERE id = $id");
    $_SESSION['success'] = "Data tanaman berhasil dihapus";
    header("Location: ?page=admin_manajemen_tanaman");
    exit();
}

// Get all plants
$tanaman = [];
$result = mysqli_query($conn, "SELECT * FROM tanaman ORDER BY nama");
if ($result) {
    $tanaman = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get data for editing
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM tanaman WHERE id = $id");
    if ($result) {
        $edit_data = mysqli_fetch_assoc($result);
    }
}
?>

<!-- HTML/Template Section -->
<section class="py-10">
    <h1 class="text-2xl font-bold mb-6">Manajemen Tanaman</h1>
    
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <!-- Form -->
    <div class="bg-white p-6 rounded shadow mb-8">
        <h2 class="text-xl font-semibold mb-4"><?= $edit_data ? 'Edit Tanaman' : 'Tambah Tanaman Baru' ?></h2>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($edit_data['id']) ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-2">Nama Tanaman *</label>
                    <input type="text" name="nama" class="w-full p-2 border rounded" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['nama']) : '' ?>" required>
                </div>
                
                <div>
                    <label class="block mb-2">Harga (Rp) *</label>
                    <input type="number" name="harga" min="0" class="w-full p-2 border rounded" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['harga']) : '' ?>" required>
                </div>
                
                <div>
                    <label class="block mb-2">Stok *</label>
                    <input type="number" name="stok" min="0" class="w-full p-2 border rounded" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['stok']) : '' ?>" required>
                </div>
                
                <div>
                    <label class="block mb-2">Gambar <?= !$edit_data ? '*' : '' ?></label>
                    <input type="file" name="gambar" class="w-full p-2 border rounded" <?= !$edit_data ? 'required' : '' ?>>
                    <?php if ($edit_data && !empty($edit_data['gambar'])): ?>
                        <div class="mt-2">
                            <img src="uploads/<?= htmlspecialchars($edit_data['gambar']) ?>" class="h-20">
                            <p class="text-sm text-gray-500">Gambar saat ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Cara Menanam *</label>
                <textarea name="cara_menanam" class="w-full p-2 border rounded" rows="4" required><?= 
                    $edit_data ? htmlspecialchars($edit_data['cara_menanam']) : '' 
                ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-2">Saran Tempat</label>
                    <input type="text" name="saran_tempat" class="w-full p-2 border rounded" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['saran_tempat']) : '' ?>">
                </div>
                
                <div>
                    <label class="block mb-2">Suhu</label>
                    <input type="text" name="suhu" class="w-full p-2 border rounded" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['suhu']) : '' ?>">
                </div>
                
                <div>
                    <label class="block mb-2">Kelembapan</label>
                    <input type="text" name="kelembapan" class="w-full p-2 border rounded" 
                           value="<?= $edit_data ? htmlspecialchars($edit_data['kelembapan']) : '' ?>">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit" name="<?= $edit_data ? 'edit_tanaman' : 'tambah_tanaman' ?>" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <?= $edit_data ? 'Update' : 'Simpan' ?>
                </button>
                
                <?php if ($edit_data): ?>
                    <a href="?page=admin_manajemen_tanaman" class="ml-2 text-gray-600 hover:text-gray-800">
                        Batal
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <!-- Plant List -->
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Daftar Tanaman</h2>
        
        <?php if (empty($tanaman)): ?>
            <p class="text-gray-500">Belum ada data tanaman</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-3 text-left">Nama</th>
                            <th class="p-3 text-left">Harga</th>
                            <th class="p-3 text-left">Stok</th>
                            <th class="p-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tanaman as $t): ?>
                            <tr class="border-b">
                                <td class="p-3"><?= htmlspecialchars($t['nama']) ?></td>
                                <td class="p-3">Rp <?= number_format($t['harga'], 0, ',', '.') ?></td>
                                <td class="p-3"><?= htmlspecialchars($t['stok']) ?></td>
                                <td class="p-3">
                                    <a href="?page=admin_manajemen_tanaman&action=edit&id=<?= $t['id'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" 
                                       class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                                    <a href="?page=admin_manajemen_tanaman&action=hapus&id=<?= $t['id'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" 
                                       class="text-red-500 hover:text-red-700"
                                       onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>