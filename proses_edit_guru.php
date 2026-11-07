<?php
// proses_edit_guru.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nip = $_POST['nip'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$status_aktif = $_POST['status_aktif'];


$sql = "UPDATE t_guru SET
        nip = '$nip',
        nama = '$nama',
        email = '$email',
        status_aktif = '$status_aktif'
        WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {

    $_SESSION['pesan_sukses'] = "Kelola Guru berhasil diubah!";
    header("Location: kelola_guru.php");
    exit;

} else {

    $_SESSION['pesan_error'] = "Tahun ajaran gagal diubah!";
    header("Location: edit_guru.php?id=$id");
    exit;
}
?>