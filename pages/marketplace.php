<?php
global $conn;
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';
$query = "SELECT * FROM tanaman WHERE nama LIKE '%$keyword%' AND stok > 0";
$result = mysqli_query($conn, $query);
$tanaman = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<section class="py-10">
    <!-- Navbar Marketplace -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Marketplace</h1>
        <div class="flex items-center space-x-4">
            <a href="?page=keranjang" class="flex items-center text-green-600 hover:text-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="ml-1">Keranjang</span>
            </a>
        </div>
    </div>

    <form method="GET" class="mb-6">
        <input type="hidden" name="page" value="Marketplace">
        <input type="text" name="keyword" class="border p-2 rounded" placeholder="Cari tanaman..." value="<?php echo htmlspecialchars($keyword); ?>">
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Cari</button>
    </form>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($tanaman as $item): ?>
            <div class="border p-4 rounded shadow hover:shadow-lg transition">
                <img src="uploads/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama']); ?>" class="w-full h-48 object-cover mb-2">
                <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($item['nama']); ?></h2>
                <p>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                <a href="?page=detail_tanaman&id=<?php echo $item['id']; ?>" class="bg-green-500 text-white px-4 py-2 rounded mt-2 inline-block">Lihat Detail</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>