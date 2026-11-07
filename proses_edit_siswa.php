<?php
// proses_edit_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_POST['id'];
$nis = $_POST['nis'];
$nisn = $_POST['nisn'];
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$alamat = $_POST['alamat'];
$status_aktif = $_POST['status_aktif'];

$sql = "UPDATE t_siswa SET
        nis = '$nis',
        nisn = '$nisn',
        nama = '$nama',
        jenis_kelamin = '$jenis_kelamin',
        tanggal_lahir = '$tanggal_lahir',
        alamat = '$alamat',
        status_aktif = '$status_aktif',
        updated_at = CURRENT_TIMESTAMP
        WHERE id = '$id'";

if (mysqli_query($koneksi, $sql)) {

    $_SESSION['pesan_sukses'] = "Data siswa berhasil diperbarui.";
    header("Location: kelola_siswa.php");
    exit;

} else {

   $_SESSION['pesan_error'] = "Data siswa gagal diperbarui.";
    header("Location: kelola_siswa.php");
    exit;

}
?>