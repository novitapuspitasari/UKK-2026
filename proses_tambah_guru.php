
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$nip = $_POST['nip'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$status_aktif = $_POST['status_aktif'];
$user_id = $_POST['user_id'];

// Simpan data guru
$sql = "INSERT INTO t_guru
        (user_id, nip, nama, email, status_aktif)
        VALUES
        ('$user_id', '$nip', '$nama', '$email', '$status_aktif')";

$hasil = mysqli_query($koneksi, $sql);

if ($hasil) {
    header("Location: kelola_guru.php");
    exit;
} else {
    echo "Gagal menambahkan data guru: "
         . mysqli_error($koneksi);
}
?>
