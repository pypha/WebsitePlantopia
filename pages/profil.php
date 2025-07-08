<?php
// 1. START SESSION DAN ERROR REPORTING
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. KONEKSI DATABASE
$db_host = "sql302.infinityfree.com";
$db_user = "if0_39349773";
$db_pass = "vRZqGdsj3mD3ih";
$db_name = "if0_39349773_plantopia";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("<div class='p-4 bg-red-100 text-red-700 rounded'>Error: Gagal terhubung ke database. " . mysqli_connect_error() . "</div>");
}

// 3. CEK SESSION USER
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: /websiteplantopia/pages/masuk.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// 4. AMBIL DATA PENGGUNA
$query = "SELECT * FROM pengguna WHERE id=?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Error dalam persiapan query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $user_id);
if (!mysqli_stmt_execute($stmt)) {
    die("Error dalam eksekusi query: " . mysqli_stmt_error($stmt));
}
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("<div class='p-4 bg-red-100 text-red-700 rounded'>Error: " . mysqli_error($conn) . "</div>");
}

$pengguna = mysqli_fetch_assoc($result);
if (!$pengguna) {
    die("<div class='p-4 bg-red-100 text-red-700 rounded'>Error: Profil tidak ditemukan</div>");
}

// 5. HANDLE EDIT MODE
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == 'true';

// 6. PROSES UPDATE PROFIL
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profil'])) {
    // Validasi input
    $nama = trim($_POST['nama'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    
    if (empty($nama)) {
        $error = "Nama tidak boleh kosong";
    } else {
        if ($role === 'pengguna') {
            $alamat = trim($_POST['alamat'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            $query = "UPDATE pengguna SET nama=?, alamat=?, bio=?, no_telepon=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $nama, $alamat, $bio, $no_telepon, $user_id);
        } else {
            $query = "UPDATE pengguna SET nama=?, no_telepon=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $nama, $no_telepon, $user_id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Profil berhasil diperbarui";
            header("Location: /websiteplantopia/pages/profil.php");
            exit();
        } else {
            $error = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- TAMPILAN NOTIFIKASI -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <section class="py-10">
            <h1 class="text-3xl font-bold mb-4">Profil</h1>
            
            <?php if (!$edit_mode): ?>
                <!-- MODE LIHAT PROFIL -->
                <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
                    <div class="mb-4">
                        <h2 class="text-xl font-semibold">
                            <?php echo !empty($pengguna['nama']) ? htmlspecialchars($pengguna['nama']) : '<span class="text-red-500">Nama belum diisi</span>'; ?>
                        </h2>
                    </div>
                    
                    <?php if ($role === 'pengguna'): ?>
                        <div class="mb-4">
                            <label class="block text-gray-600 mb-1">Alamat</label>
                            <p class="border p-2 rounded bg-gray-50 min-h-10">
                                <?php echo !empty($pengguna['alamat']) ? htmlspecialchars($pengguna['alamat']) : '<span class="text-gray-400">- Belum diisi -</span>'; ?>
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-600 mb-1">Bio</label>
                            <p class="border p-2 rounded bg-gray-50 min-h-10">
                                <?php echo !empty($pengguna['bio']) ? htmlspecialchars($pengguna['bio']) : '<span class="text-gray-400">- Belum diisi -</span>'; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label class="block text-gray-600 mb-1">No. Telepon</label>
                        <p class="border p-2 rounded bg-gray-50">
                            <?php echo !empty($pengguna['no_telepon']) ? htmlspecialchars($pengguna['no_telepon']) : '<span class="text-gray-400">- Belum diisi -</span>'; ?>
                        </p>
                    </div>
                    
                    <div class="flex gap-2 mt-6">
                        <a href="/websiteplantopia/pages/profil.php?edit=true" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition duration-200">
                            Edit Profil
                        </a>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- MODE EDIT PROFIL -->
                <form method="POST" action="/websiteplantopia/pages/profil.php" class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
                    <input type="hidden" name="update_profil" value="1">
                    
                    <div class="mb-4">
                        <label class="block text-gray-600 mb-1">Nama <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="<?php echo htmlspecialchars($pengguna['nama'] ?? ''); ?>" 
                               class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-300" required>
                    </div>
                    
                    <?php if ($role === 'pengguna'): ?>
                        <div class="mb-4">
                            <label class="block text-gray-600 mb-1">Alamat</label>
                            <textarea name="alamat" class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-300 h-24"><?php 
                                echo htmlspecialchars($pengguna['alamat'] ?? ''); 
                            ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-600 mb-1">Bio</label>
                            <textarea name="bio" class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-300 h-24"><?php 
                                echo htmlspecialchars($pengguna['bio'] ?? ''); 
                            ?></textarea>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label class="block text-gray-600 mb-1">No. Telepon</label>
                        <input type="tel" name="no_telepon" value="<?php echo htmlspecialchars($pengguna['no_telepon'] ?? ''); ?>" 
                               class="border p-2 w-full rounded focus:ring-2 focus:ring-blue-300">
                        <p class="text-gray-500 text-sm mt-1">Contoh: 081234567890</p>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition duration-200">
                            Simpan Perubahan
                        </button>
                        <a href="/websiteplantopia/pages/profil.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition duration-200">
                            Batal
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
