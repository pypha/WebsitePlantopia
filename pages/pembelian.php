<section class="py-10">
    <h1 class="text-3xl font-bold mb-4">Detail Pembelian</h1>
    <form method="POST" class="max-w-md mx-auto">
        <div class="mb-4">
            <label class="block mb-1">Nama</label>
            <input type="text" name="nama" class="border p-2 w-full rounded" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Alamat</label>
            <textarea name="alamat" class="border p-2 w-full rounded" required></textarea>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Metode Pengiriman</label>
            <select name="metode_pengiriman" class="border p-2 w-full rounded" required>
                <option value="standar">Standar</option>
                <option value="ekspres">Ekspres</option>
            </select>
        </div>
        <button type="submit" name="buat_pesanan" class="bg-green-500 text-white px-4 py-2 rounded">Lanjutkan ke Pembayaran</button>
    </form>
</section>