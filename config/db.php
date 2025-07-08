<?php
$host = 'sql302.infinityfree.com';
$user = 'if0_39349773';
$password = 'vRZqGdsj3mD3ih';
$database = 'if0_39349773_plantopia';

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
