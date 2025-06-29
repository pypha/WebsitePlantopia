<?php
global $conn;

// Authorization check - hanya admin yang bisa akses
if (!cekRoleAdmin()) {
    header('Location: ?page=beranda');
    exit;
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_pengguna'])) {
        // [Kode sebelumnya...]
        $role = $_POST['role']; // Ambil role dari form
    }
    elseif (isset($_POST['update_role'])) {
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
        $new_role = mysqli_real_escape_string($conn, $_POST['role']);
        
        $query = "UPDATE pengguna SET role='$new_role' WHERE id='$user_id'";
        mysqli_query($conn, $query);
    }
}

// Get all users
$query = "SELECT * FROM pengguna ORDER BY role DESC, nama ASC";
$users = mysqli_fetch_all(mysqli_query($conn, $query), MYSQLI_ASSOC);
?>

<section class="py-10">
    <h1 class="text-3xl font-bold mb-6">Manajemen Pengguna</h1>

    <!-- Add User Form -->
    <form method="POST" enctype="multipart/form-data" class="mb-8 bg-white p-4 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-3">Tambah Pengguna Baru</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- [Field lainnya...] -->
            <div>
                <label class="block mb-1">Role</label>
                <select name="role" class="w-full p-2 border rounded" required>
                    <option value="pengguna">Pengguna</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <button type="submit" name="tambah_pengguna" class="mt-4 bg-green-500 text-white px-4 py-2 rounded">
            Tambah Pengguna
        </button>
    </form>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Nama</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Role</th>
                    <th class="p-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="border-t">
                    <td class="p-3"><?= htmlspecialchars($user['nama']) ?></td>
                    <td class="p-3"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="p-3">
                        <form method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role" class="p-1 border rounded">
                                <option value="pengguna" <?= $user['role'] === 'pengguna' ? 'selected' : '' ?>>Pengguna</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <button type="submit" name="update_role" class="bg-blue-500 text-white px-2 py-1 rounded text-sm">
                                Update
                            </button>
                        </form>
                    </td>
                    <td class="p-3">
                        <a href="?page=edit_pengguna&id=<?= $user['id'] ?>" class="text-blue-500 mr-2">Edit</a>
                        <a href="?page=hapus_pengguna&id=<?= $user['id'] ?>" class="text-red-500" onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>