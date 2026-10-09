<?php
// buat_user_admin.php
// Jalankan file  ini SATU KALI saja lewat browser untuk membuat user admin
include 'config/koneksi.php';

$name     = 'Siswa';
$email = 'siswa@gmail.com';
$password = password_hash('siswa123', PASSWORD_DEFAULT);
$role     ='siswa';

$sql = "INSERT INTO t_users ( name, email, password, role)";
$sql .= " VALUES ( '$name', '$email', '$password' , '$role')";

if (mysqli_query($koneksi, $sql)) {
    echo 'User admin berhasil dibuat. Silakan hapus file ini.';
} else {
    echo 'Gagal membuat user: ' .mysqli_error($koneksi);
}
?>