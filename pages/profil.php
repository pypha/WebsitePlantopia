<?php
global $conn;
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM pengguna WHERE id='$user_id'";
$result = mysqli_query($conn, $query);
$pengguna = mysqli_fetch_assoc($result);
?>

<section class="py-10">
    <h1 class="text-3xl font-bold mb-4">Profil</h1>
    <form method="POST" class="max-w-md mx-auto">
        <div class="mb-4">
            <label class="block mb-1">Nama</label>
            <input type="text" name="nama" value="<?php echo htmlspecialchars($pengguna['nama']); ?>" class="border p-2 w-full rounded" required>
        </div>
        <?php if ($_SESSION['role'] === 'pengguna'): ?>
            <div class="mb-4">
                <label class="block mb-1">Alamat</label>
                <textarea name="alamat" class="border p-2 w-full rounded"><?php echo htmlspecialchars($pengguna['alamat']); ?></textarea>
            </div>
            <div class="mb-4">
                <label class="block mb-1">Bio</label>
                <textarea name="bio" class="border p-2 w-full rounded"><?php echo htmlspecialchars($pengguna['bio']); ?></textarea>
            </div>
        <?php endif; ?>
        <div class="mb-4">
            <label class="block mb-1">No. Telepon</label>
            <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($pengguna['no_telepon']); ?>" class="border p-2 w-full rounded">
        </div>
        <button type="submit" name="update_profil" class="bg-green-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
    </form>
</section>