<?php
global $conn;

// Tentukan direktori upload berdasarkan root proyek
$base_dir = dirname(dirname(__FILE__)); // Naik satu level dari 'pages' ke root 'websiteplantopia'
$upload_dir = 'uploads/';
$full_upload_dir = $base_dir . '/' . $upload_dir;
if (!file_exists($full_upload_dir)) {
    mkdir($full_upload_dir, 0777, true); // Gunakan 0777 untuk tes lokal
    // echo "Debug: Created directory $full_upload_dir<br>";
}

// Query untuk mendapatkan postingan
$query = "SELECT p.*, u.nama FROM postingan_komunitas p JOIN pengguna u ON p.user_id = u.id ORDER BY waktu DESC";
$result = mysqli_query($conn, $query);
$postingan = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<section class="py-10 bg-green-50">
    <h1 class="text-3xl font-bold mb-4 text-center">Komunitas Plantopia</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="max-w-2xl mx-auto mb-4 p-4 bg-red-100 text-red-700 rounded">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="max-w-2xl mx-auto mb-4 p-4 bg-green-100 text-green-700 rounded">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="max-w-2xl mx-auto mb-6 p-4 bg-white rounded-lg shadow">
        <textarea name="konten" class="border p-2 w-full rounded mb-2" placeholder="Tulis sesuatu..." required></textarea>
        <input type="file" name="file" class="border p-2 w-full rounded mb-2" accept="image/*">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit" name="unggah_postingan" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Unggah</button>
    </form>
    
    <?php foreach ($postingan as $post): ?>
        <div class="max-w-2xl mx-auto mb-4 p-4 bg-white rounded-lg shadow">
            <div class="flex items-center mb-2">
                <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mr-2">
                    <span class="text-green-800 font-bold"><?php echo substr($post['nama'], 0, 1); ?></span>
                </div>
                <div>
                    <p class="font-semibold text-green-800"><?php echo htmlspecialchars($post['nama']); ?></p>
                    <p class="text-gray-500 text-sm"><?php echo date('d M Y H:i', strtotime($post['waktu'])); ?></p>
                </div>
            </div>
            
            <?php
            $content_parts = explode('|', $post['konten']);
            $text_content = htmlspecialchars($content_parts[0]);
            $media_path = isset($content_parts[1]) ? $content_parts[1] : '';
            
            echo "<p class='mt-2 mb-3'>$text_content</p>";
            
            if (!empty($media_path) && $post['tipe'] === 'gambar') {
                $full_media_path = $full_upload_dir . basename($media_path);
                if (file_exists($full_media_path)) {
                    $relative_media_path = '/websiteplantopia/' . $upload_dir . basename($media_path);
                    echo "<img src='$relative_media_path' alt='Posted Image' class='w-full max-h-96 object-contain mt-2 rounded border' onerror='this.style.display=\"none\";console.log(\"Image load error: $relative_media_path\");'>";
                }
            }
            ?>
            
            <?php if ($post['user_id'] == $_SESSION['user_id'] || cekRoleAdmin()): ?>
                <div class="mt-3 pt-3 border-t">
                    <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
                        <a href="?page=komunitas&edit_postingan=<?php echo $post['id']; ?>" class="text-blue-500 mr-3 hover:text-blue-700">Edit</a>
                    <?php endif; ?>
                    <a href="?page=komunitas&hapus_postingan=<?php echo $post['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Yakin ingin menghapus postingan ini?')">Hapus</a>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['edit_postingan']) && $_GET['edit_postingan'] == $post['id'] && $post['user_id'] == $_SESSION['user_id']): ?>
                <form method="POST" enctype="multipart/form-data" class="mt-3">
                    <input type="hidden" name="postingan_id" value="<?php echo $post['id']; ?>">
                    <input type="hidden" name="tipe" value="<?php echo $post['tipe']; ?>">
                    <textarea name="konten" class="border p-2 w-full rounded mb-2" required><?php echo $text_content; ?></textarea>
                    <input type="file" name="file" class="border p-2 w-full rounded mb-2" accept="image/*">
                    <button type="submit" name="simpan_edit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" onclick="return confirm('Yakin ingin menyimpan perubahan?')">Simpan Perubahan</button>
                </form>
            <?php endif; ?>
            
            <!-- Form untuk menambahkan komentar -->
            <form method="POST" class="mt-3">
                <input type="hidden" name="postingan_id" value="<?php echo $post['id']; ?>">
                <textarea name="komentar" class="border p-2 w-full rounded mb-2" placeholder="Tambah komentar..." required></textarea>
                <button type="submit" name="tambah_komentar" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Kirim Komentar</button>
            </form>
            
            <!-- Menampilkan komentar -->
            <?php
            $query_komentar = "SELECT k.*, u.nama FROM komentar k JOIN pengguna u ON k.user_id = u.id WHERE k.postingan_id = '{$post['id']}' ORDER BY k.waktu DESC";
            $result_komentar = mysqli_query($conn, $query_komentar);
            if (mysqli_num_rows($result_komentar) > 0) {
                echo "<div class='mt-3'>";
                while ($komentar = mysqli_fetch_assoc($result_komentar)) {
                    echo "<p class='ml-4 mt-2 text-gray-600'><strong>" . htmlspecialchars($komentar['nama']) . ":</strong> " . htmlspecialchars($komentar['komentar']) . " <span class='text-xs text-gray-400'>" . date('d M Y H:i', strtotime($komentar['waktu'])) . "</span></p>";
                }
                echo "</div>";
            }
            ?>
        </div>
    <?php endforeach; ?>
</section>