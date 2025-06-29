<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['masuk'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sandi = $_POST['sandi'];

    $query = "SELECT * FROM pengguna WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($sandi, $user['sandi'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: ?page=beranda");
            exit;
        } else {
            $error = "Kata sandi salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<section class="min-h-screen bg-gradient-to-r from-green-100 to-green-500 flex items-center justify-center p-4">
    <div class="flex flex-col md:flex-row bg-white rounded-2xl shadow-lg overflow-hidden w-full max-w-4xl">
        <!-- Welcome Back Section -->
        <div class="w-full md:w-1/2 bg-green-900 text-white p-8 flex flex-col justify-center items-center">
            <h1 class="text-3xl font-bold mb-4">Welcome Back!</h1>
            <p class="text-center mb-6">Enter your personal details to use all of site features.</p>
            <a href="?page=masuk" class="bg-white text-green-900 px-6 py-3 rounded-lg hover:bg-gray-200 font-semibold">Sign In</a>
        </div>

        <!-- Login Form -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-2xl font-bold mb-4 text-green-800 text-center">Sign In</h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Password</label>
                    <input type="password" name="sandi" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                </div>
                <button type="submit" name="masuk" class="w-full bg-green-700 text-white p-2 rounded-lg hover:bg-green-800 transition duration-200">Sign In</button>
            </form>
            <p class="mt-4 text-center text-gray-600">Forgot password? <a href="?page=reset_sandi" class="text-green-600 hover:underline">Reset Password</a></p>
            <p class="mt-2 text-center text-gray-600">Don't have an account? <a href="?page=daftar" class="text-green-600 hover:underline">Sign Up</a></p>
        </div>
    </div>
</section>