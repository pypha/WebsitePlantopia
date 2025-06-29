<?php
global $conn;
$pesanan_id = mysqli_real_escape_string($conn, $_GET['pesanan_id'] ?? '');  // Pastikan menggunakan 'pesanan_id'

if (empty($pesanan_id) || !is_numeric($pesanan_id)) {
    die("Parameter ID pesanan tidak valid.");
}

// Query updated to use pesanan_id instead of id
$query = "SELECT p.*, 
                 (SELECT SUM(t.harga * k.jumlah) 
                  FROM keranjang k 
                  JOIN tanaman t ON k.tanaman_id = t.id 
                  WHERE k.user_id = p.user_id) as total_harga
          FROM pesanan p 
          WHERE p.pesanan_id='$pesanan_id' AND p.user_id='{$_SESSION['user_id']}'";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error dalam query: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 0) {
    die("Pesanan tidak ditemukan atau Anda tidak memiliki akses.");
}

$pesanan = mysqli_fetch_assoc($result);

// Gunakan total_harga dari database jika ada, jika tidak hitung manual
$total_harga = $pesanan['total_harga'] ?? 0;

// Generate QR Code
$qrData = "ID Pesanan: " . $pesanan['pesanan_id'] . "\n";
$qrData .= "Nama: " . $pesanan['nama'] . "\n";
$qrData .= "Alamat: " . $pesanan['alamat'] . "\n";
$qrData .= "Total: Rp " . number_format($total_harga, 0, ',', '.') . "\n";
$qrData .= "Status: " . $pesanan['status'];
// Coba URL alternatif:
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
?>

<section class="py-10 text-center">
    <h1 class="text-3xl font-bold mb-6">Pembayaran</h1>
    
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
        <div class="text-left mb-4 space-y-2">
            <p><strong>ID Pesanan:</strong> <?= htmlspecialchars($pesanan['pesanan_id']) ?></p>
            <p><strong>Nama:</strong> <?= htmlspecialchars($pesanan['nama']) ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($pesanan['alamat']) ?></p>
            <p><strong>Metode Pengiriman:</strong> <?= htmlspecialchars($pesanan['metode_pengiriman']) ?></p>
            <p><strong>Status:</strong> <span class="px-2 py-1 rounded <?= $pesanan['status'] === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                <?= htmlspecialchars($pesanan['status']) ?>
            </span></p>
        </div>

        <div class="border-2 border-green-200 p-4 inline-block mb-4 bg-white rounded-lg">
            <img src="<?= $qrCodeUrl ?>" alt="QR Code Pembayaran" class="mx-auto" style="width: 200px; height: 200px;">
            <p class="text-xs text-gray-500 mt-2">Scan untuk verifikasi pembayaran</p>
        </div>

        <div class="space-y-3">
            <a href="<?= $qrCodeUrl ?>" download="payment_<?= $pesanan['pesanan_id'] ?>.png"
               class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg inline-block transition">
               <i class="fas fa-download mr-2"></i>Unduh QR Code
            </a>
            <a href="?page=pesanan" class="text-green-600 hover:underline inline-block">Kembali ke Daftar Pesanan</a>
        </div>
    </div>
</section>