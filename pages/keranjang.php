<?php
$keranjang = ambilKeranjang($_SESSION['user_id']);
?>

<section class="py-10">
    <h1 class="text-3xl font-bold mb-4">Keranjang Belanja</h1>
    <form method="POST">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Tanaman</th>
                    <th class="border p-2">Harga</th>
                    <th class="border p-2">Jumlah</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($keranjang as $item): ?>
                    <tr>
                        <td class="border p-2"><?php echo htmlspecialchars($item['nama']); ?></td>
                        <td class="border p-2">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td class="border p-2">
                            <input type="number" name="jumlah[<?php echo $item['id']; ?>]" value="<?php echo $item['jumlah']; ?>" min="0" class="border p-1 w-16">
                        </td>
                        <td class="border p-2">
                            <button type="submit" name="update_keranjang" class="bg-green-500 text-white px-2 py-1 rounded">Update</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
    <a href="?page=pembelian" class="bg-green-500 text-white px-4 py-2 rounded mt-4 inline-block">Lanjutkan ke Pembelian</a>
</section>