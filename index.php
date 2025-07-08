<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';

// Tentukan rute
$rute = isset($_GET['page']) ? $_GET['page'] : 'beranda';

// Tangani pengiriman formulir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['daftar'])) {
        tanganiDaftar($_POST);
    } elseif (isset($_POST['masuk'])) {
        tanganiMasuk($_POST);
    } elseif (isset($_POST['reset_sandi'])) {
        tanganiResetSandi($_POST);
    } elseif (isset($_POST['tambah_keranjang'])) {
        tambahKeKeranjang($_POST);
    } elseif (isset($_POST['update_keranjang'])) {
        updateKeranjang($_POST);
    } elseif (isset($_POST['buat_pesanan'])) {
        buatPesanan($_POST);
    } elseif (isset($_POST['unggah_postingan'])) {
        unggahPostingan($_POST);
    } elseif (isset($_POST['tambah_komentar'])) {
        tambahKomentar($_POST);
    } elseif (isset($_POST['update_profil'])) {
        updateProfil($_POST);
    } elseif (isset($_POST['tambah_tanaman'])) {
        if (cekRoleAdmin()) {
            tambahTanaman($_POST);
        } else {
            header('Location: ?page=beranda');
            exit;
        }
    } elseif (isset($_POST['update_status_pesanan'])) {
        if (cekRoleAdmin()) {
            updateStatusPesanan($_POST);
        } else {
            header('Location: ?page=beranda');
            exit;
        }
    } elseif (isset($_POST['simpan_edit'])) {
        editPostingan($_POST); // Fungsi baru untuk edit
    }
}

// Tangani penghapusan melalui GET
if (isset($_GET['hapus_postingan'])) {
    $postingan_id = intval($_GET['hapus_postingan']); // Pastikan ID adalah integer
    if ($postingan_id > 0) {
        hapusPostingan($postingan_id); // Fungsi baru untuk hapus
    }
    header('Location: ?page=komunitas');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantopia</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50 font-sans">
    <!-- Navigasi -->
    <nav class="bg-green-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="?page=beranda" class="text-2xl font-bold">Plantopia</a>
            <div class="space-x-4">
                <a href="?page=kontak" class="hover:underline">Contact Us</a>
                <a href="?page=Marketplace" class="hover:underline">Marketplace</a>
                <a href="?page=komunitas" class="hover:underline">Komunitas</a>
                <?php if (sudahMasuk()): ?>
                    <a href="?page=profil" class="hover:underline">Profil</a>
                    <?php if (cekRoleAdmin()): ?>
                        <a href="?page=admin_manajemen_tanaman" class="hover:underline">Manajemen Tanaman</a>
                        <a href="?page=admin_manajemen_pesanan" class="hover:underline">Manajemen Pesanan</a>
                    <?php endif; ?>
                    <a href="?page=keluar" class="hover:underline">Keluar</a>
                <?php else: ?>
                    <a href="?page=masuk" class="hover:underline">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <main class="container mx-auto p-6">
        <?php
        switch ($rute) {
            case 'beranda':
                include 'pages/beranda.php';
                break;
            case 'kontak':
                include 'pages/kontak.php';
                break;
            case 'Marketplace':
                if (sudahMasuk()) {
                    include 'pages/Marketplace.php';
                } else {
                    header('Location: ?page=masuk');
                    exit;
                }
                break;
            case 'detail_tanaman':
                if (sudahMasuk()) {
                    include 'pages/detail_tanaman.php';
                } else {
                    header('Location: ?page=masuk');
                    exit;
                }
                break;
            case 'keranjang':
                if (sudahMasuk()) {
                    include 'pages/keranjang.php';
                } else {
                    header('Location: ?page=masuk');
                    exit;
                }
                break;
            case 'pembelian':
                if (sudahMasuk()) {
                    include 'pages/pembelian.php';
                } else {
                    header('Location: ?page=masuk');
                    exit;
                }
                break;
            case 'pembayaran':
                if (sudahMasuk()) {
                    include 'pages/pembayaran.php';
                } else {
                    header('Location: ?page=masuk');
                    exit;
                }
                break;
            case 'komunitas':
                if (sudahMasuk()) {
                    include 'pages/komunitas.php';
                } else {
                    header('Location: ?page=masuk');
                    exit;
                }
                break;
            case 'daftar':
                include 'pages/daftar.php';
                break;
            case 'masuk':
                include 'pages/masuk.php';
                break;
            case 'reset_sandi':
                include 'pages/reset_sandi.php';
                break;
            case 'admin_manajemen_pengguna':
                if (cekRoleAdmin()) {
                    include 'pages/admin_manajemen_pengguna.php';
                } else {
                    header('Location: ?page=beranda');
                }
                break;
            case 'profil':
                if (sudahMasuk()) {
                    include 'pages/profil.php';
                } else {
                    header('Location: ?page=masuk');
                    exit;
                }
                break;
            case 'admin_manajemen_tanaman':
                if (sudahMasuk() && cekRoleAdmin()) {
                    include 'pages/admin_manajemen_tanaman.php';
                } else {
                    header('Location: ?page=beranda');
                    exit;
                }
                break;
            case 'admin_manajemen_pesanan':
                if (sudahMasuk() && cekRoleAdmin()) {
                    include 'pages/admin_manajemen_pesanan.php';
                } else {
                    header('Location: ?page=beranda');
                    exit;
                }
                break;
            case 'keluar':
                keluar();
                header('Location: ?page=beranda');
                exit;
                break;
            default:
                include 'pages/beranda.php';
                break;
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="bg-green-600 text-white p-4 text-center">
        <p>© 2025 Plantopia. Semua hak dilindungi.</p>
    </footer>
</body>
</html>
