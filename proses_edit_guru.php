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

    $_SESSION['pesan_sukses'] = "Tahun ajaran berhasil diubah!";
    header("Location: kelola_tahun_ajaran.php");
    exit;

} else {

    $_SESSION['pesan_error'] = "Tahun ajaran gagal diubah!";
    header("Location: edit_tahun_ajaran.php?id=$id");
    exit;
}
?>