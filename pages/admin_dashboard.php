<?php
global $conn;

// Authorization check - hanya admin yang bisa akses
if (!cekRoleAdmin()) {
    header('Location: ?page=beranda');
    exit;
}

// Ambil statistik
$query_users = "SELECT COUNT(*) as total FROM pengguna";
$total_users = mysqli_fetch_assoc(mysqli_query($conn, $query_users))['total'];

$query_orders = "SELECT COUNT(*) as total FROM pesanan";
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, $query_orders))['total'];

$query_plants = "SELECT COUNT(*) as total FROM tanaman";
$total_plants = mysqli_fetch_assoc(mysqli_query($conn, $query_plants))['total'];

$query_recent_orders = "SELECT p.*, u.nama as nama_pengguna FROM pesanan p JOIN pengguna u ON p.user_id = u.id ORDER BY p.tanggal DESC LIMIT 5";
$recent_orders = mysqli_fetch_all(mysqli_query($conn, $query_recent_orders), MYSQLI_ASSOC);
?>

<section class="py-10">
    <h1 class="text-3xl font-bold mb-6">Dashboard Admin</h1>
    
    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Total Pengguna</h3>
            <p class="text-2xl font-bold text-green-600"><?= $total_users ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Total Pesanan</h3>
            <p class="text-2xl font-bold text-green-600"><?= $total_orders ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Total Tanaman</h3>
            <p class="text-2xl font-bold text-green-600"><?= $total_plants ?></p>
        </div>
    </div>
    
    <!-- Pesanan Terbaru -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
        <h2 class="text-xl font-semibold p-4 bg-gray-100">Pesanan Terbaru</h2>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left">ID Pesanan</th>
                    <th class="p-3 text-left">Nama</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                <tr class="border-t">
                    <td class="p-3"><?= $order['id'] ?></td>
                    <td class="p-3"><?= htmlspecialchars($order['nama_pengguna']) ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded <?= 
                            $order['status'] === 'selesai' ? 'bg-green-100 text-green-800' : 
                            ($order['status'] === 'dibatalkan' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') 
                        ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </td>
                    <td class="p-3"><?= date('d M Y', strtotime($order['tanggal'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="?page=admin_manajemen_pengguna" class="bg-white p-4 rounded-lg shadow hover:bg-gray-50 transition">
            <h3 class="font-semibold text-green-600">Manajemen Pengguna</h3>
            <p class="text-sm text-gray-600">Kelola data pengguna</p>
        </a>
        <a href="?page=admin_manajemen_tanaman" class="bg-white p-4 rounded-lg shadow hover:bg-gray-50 transition">
            <h3 class="font-semibold text-green-600">Manajemen Tanaman</h3>
            <p class="text-sm text-gray-600">Kelola produk tanaman</p>
        </a>
        <a href="?page=admin_manajemen_pesanan" class="bg-white p-4 rounded-lg shadow hover:bg-gray-50 transition">
            <h3 class="font-semibold text-green-600">Manajemen Pesanan</h3>
            <p class="text-sm text-gray-600">Kelola pesanan pelanggan</p>
        </a>
    </div>
</section>