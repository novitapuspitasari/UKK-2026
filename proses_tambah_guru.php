<?php
// proses_tambah_guru.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$nip = $_POST['nip'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$status_aktif = $_POST['status_aktif'];

$sql = "INSERT INTO t_guru 
        (nip, nama, email, status_aktif)
        VALUES 
        ('$nip', '$nama', '$email', '$status_aktif')";

if (mysqli_query($koneksi, $sqlguru)) {
    $nip  = $_SESSION['nip'];
    $nama     = $_SESSION['nama'];
    $email     = $_SESSION['email'];
    $status_aktif     = $_SESSION['status_aktif'];
    mysqli_query($koneksi, $log);

    header("Location: kelola_guru.php");
    exit;
} else {
    echo "Gagal menambahkan data guru: " . mysqli_error($koneksi);
}
?>