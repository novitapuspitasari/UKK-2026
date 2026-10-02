<?php
// proses_tambah_guru.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$nip = $_POST['nis'];
$nama = $_POST['nisn'];
$email = $_POST['nama'];
$email = $_POST['jenis_kelamin'];
$email = $_POST['tanggal_lahir'];
$email = $_POST['alamat'];
$status_aktif = $_POST['status_aktif'];

$sql = "INSERT INTO t_guru 
        (nip, nisn, nama, jenis_kelamin , tanggal_lahir, alamat, status_aktif)
        VALUES 
        ('$nis', '$nisn', '$nama', '$jenis_kelamin', '$tanggal_lahir','$alamat' '$status_aktif')";

if (mysqli_query($koneksi, $sqlsiswa)) {
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