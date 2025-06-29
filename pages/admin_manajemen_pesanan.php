<?php
global $conn;
$query = "SELECT p.*, u.nama AS nama_pengguna FROM pesanan p JOIN pengguna u ON p.user_id = u.id";
$result = mysqli_query($conn, $query);
$pesanan = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<section class="py-10">
    <h1 class="text-3xl font-bold mb-4">Manajemen Pesanan</h1>
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">ID Pesanan</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Alamat</th>
                <th class="border p-2">Metode Pengiriman</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pesanan as $item): ?>
                <tr>
                    <td class="border p-2"><?php echo $item['pesanan_id']; ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($item['nama_pengguna']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($item['alamat']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($item['metode_pengiriman']); ?></td>
                    <td class="border p-2"><?php echo htmlspecialchars($item['status']); ?></td>
                    <td class="border p-2">
                        <form method="POST">
                            <input type="hidden" name="pesanan_id" value="<?php echo $item['pesanan_id']; ?>">
                            <select name="status" class="border p-1 rounded">
                                <option value="dikemas" <?php echo $item['status'] == 'dikemas' ? 'selected' : ''; ?>>Dikemas</option>
                                <option value="dibatalkan" <?php echo $item['status'] == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                                <option value="dalam_pengiriman" <?php echo $item['status'] == 'dalam_pengiriman' ? 'selected' : ''; ?>>Dalam Pengiriman</option>
                                <option value="telah_sampai" <?php echo $item['status'] == 'telah_sampai' ? 'selected' : ''; ?>>Telah Sampai</option>
                            </select>
                            <button type="submit" name="update_status_pesanan" class="bg-green-500 text-white px-2 py-1 rounded">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>