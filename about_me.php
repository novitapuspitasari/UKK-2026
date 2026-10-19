<?php
session_start();
include "config/koneksi.php";


$nama = "Novita Puspitasari ";
$kelas = "XI RPL";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Mendengarkan Musik";
$cita_cita = "Psikologi";
?>

<div class="media">
  <img class="mr-3" src="img/haechan.jpg" alt="Generic placeholder image">
  <div class="media-body">
    <h5 class="mt-0">Novita Puspitasari</h5>
  </div>
</div>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tentang Saya</title>
</head>
<body>
<h2>Tentang Saya</h2>
    <p>Nama: <?php echo $nama; ?></p>
    <p>Kelas: <?php echo $kelas; ?></p>
    <p>Sekolah: <?php echo $sekolah; ?></p>
    <p>Jurusan: <?php echo $jurusan; ?></p>
    <p>Hobi: <?php echo $hobi; ?></p>
    <p>Cita-cita: <?php echo $cita_cita; ?></p>
 <h2>Deskripsi Saya</h2>

    <p>
        Saya adalah seorang pelajar yang sedang belajar
        Rekayasa Perangkat Lunak. Saya senang mempelajari
        teknologi dan membuat website. Saya ingin terus
        belajar agar bisa menjadi Web Developer.
    </p>

    <a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>