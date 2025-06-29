<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sandi = password_hash($_POST['sandi'], PASSWORD_DEFAULT);

    $cek_email = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO pengguna (nama, email, sandi, role) VALUES ('$nama', '$email', '$sandi', 'pengguna')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            header("Location: ?page=beranda");
            exit;
        } else {
            $error = "Pendaftaran gagal!";
        }
    }
}
?>

<section class="min-h-screen bg-gradient-to-r from-green-100 to-green-500 flex items-center justify-center p-4">
    <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-lg overflow-hidden w-full max-w-4xl">
        <!-- Create Account Section -->
        <div class="w-full md:w-1/2 bg-green-900 text-white p-8 flex flex-col justify-center items-center">
            <h1 class="text-3xl font-bold mb-4">Create Account!</h1>
            <p class="text-center mb-6">Join us and start exploring all site features.</p>
            <a href="?page=daftar" class="bg-white text-green-900 px-6 py-3 rounded-lg hover:bg-gray-200 font-semibold">Sign Up</a>
        </div>

        <!-- Registration Form -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-4 text-green-800 text-center">Create Account</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-1">Name</label>
                    <input type="text" name="nama" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Password</label>
                    <input type="password" name="sandi" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <button type="submit" name="daftar" class="w-full bg-green-700 text-white p-2 rounded-lg hover:bg-green-800 transition duration-200">Sign Up</button>
            </form>
            <p class="mt-4 text-center text-gray-600">Already have an account? <a href="?page=masuk" class="text-green-600 hover:underline">Sign In</a></p>
        </div>
    </div>
</section>