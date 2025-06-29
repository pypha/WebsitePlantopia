<?php
global $conn;
$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM tanaman WHERE id='$id'";
$result = mysqli_query($conn, $query);
$tanaman = mysqli_fetch_assoc($result);
?>

<section class="py-10">
    <h1 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($tanaman['nama']); ?></h1>
    <div class="flex flex-col md:flex-row gap-6">
        <img src="uploads/<?php echo htmlspecialchars($tanaman['gambar']); ?>" alt="<?php echo htmlspecialchars($tanaman['nama']); ?>" class="w-full md:w-1/2 h-64 object-cover">
        <div>
            <p class="text-lg mb-2">Harga: Rp <?php echo number_format($tanaman['harga'], 0, ',', '.'); ?></p>
            <p class="mb-2"><strong>Cara Menanam:</strong> <?php echo htmlspecialchars($tanaman['cara_menanam']); ?></p>
            <p class="mb-2"><strong>Saran Tempat:</strong> <?php echo htmlspecialchars($tanaman['saran_tempat']); ?></p>
            <p class="mb-2"><strong>Suhu:</strong> <?php echo htmlspecialchars($tanaman['suhu']); ?></p>
            <p class="mb-2"><strong>Kelembapan:</strong> <?php echo htmlspecialchars($tanaman['kelembapan']); ?></p>
            <form method="POST">
                <input type="hidden" name="tanaman_id" value="<?php echo $tanaman['id']; ?>">
                <input type="number" name="jumlah" value="1" min="1" class="border p-2 rounded w-20">
                <button type="submit" name="tambah_keranjang" class="bg-green-500 text-white px-4 py-2 rounded mt-2">Tambah ke Keranjang</button>
            </form>
        </div>
    </div>
</section>