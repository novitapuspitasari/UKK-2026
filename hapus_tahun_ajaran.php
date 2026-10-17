
<?php
// hapus_tahun_ajaran.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil ID guru
$id = $_GET['id'] ?? '';

if ($id === '' || !ctype_digit((string) $id)) {
    header('Location: kelola_tahun_ajaran.php');
    exit;
}

// Pastikan data tahun ajaran tersedia
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if (!$data) {
    header('Location: kelola_tahun_ajaran.php');
    exit;
}

// Hapus data tahun ajaran
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);

    header('Location: kelola_tahun_ajaran.php');
    exit;
}

mysqli_stmt_close($stmt);

die('Gagal menghapus data tahun ajaran.');
?>
