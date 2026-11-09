
<?php
session_start();
include "config/koneksi.php";

$nama = "Novita Puspitasari";
$kelas = "XII RPL 1";
$sekolah = "SMK Muhammadiyah Tasikmalaya";
$jurusan = "Rekayasa Perangkat Lunak";
$hobi = "Mendengarkan Musik";
$cita_cita = "Psikologi";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tentang Saya</title>

    <!-- Bootstrap 4.6.2 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        main {
            min-height: 100vh;
        }

        .card {
            width: 100%;
        }

        .foto-profil {
            width: 240px;
            height: 240px;
            object-fit: cover;
        }
    </style>
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <!-- NAVBAR -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow rounded mb-4">

                <a class="navbar-brand font-weight-bold" href="dashboard.php">
                    <span class="mr-2">&#9733;</span> Tentang Saya
                </a>

                <a href="dashboard.php" class="btn btn-light btn-sm ml-auto">
                    Kembali ke Dashboard
                </a>

            </nav>

            <!-- KONTEN FULL SCREEN -->
            <div class="container-fluid px-0">

                <div class="card shadow border-0">

                    <!-- HEADER CARD -->
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="font-weight-bold mb-1">Profil Saya</h3>
                        <p class="mb-0">Kenali saya lebih dekat!</p>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        <!-- FOTO PROFIL -->
                        <div class="text-center mb-4">

                            <img src="img/novita.jpeg.jpeg"
                                 alt="Foto profil Novita"
                                 class="rounded-circle img-thumbnail mb-3 foto-profil">

                            <h3 class="text-primary font-weight-bold">
                                <?= htmlspecialchars($nama); ?>
                            </h3>

                            <p class="text-muted mb-2">
                                <?= htmlspecialchars($kelas); ?> |
                                <?= htmlspecialchars($jurusan); ?>
                            </p>

                            <span class="badge badge-primary p-2">
                                SMK Muhammadiyah Tasikmalaya

                            </span>

                        </div>

                        <hr>

                        <!-- INFORMASI DIRI -->
                        <h5 class="text-primary font-weight-bold mb-4">
                            Informasi Diri
                        </h5>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h6 class="text-muted">Nama Lengkap</h6>
                                        <p class="font-weight-bold mb-0">
                                            <?= htmlspecialchars($nama); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h6 class="text-muted">Kelas</h6>
                                        <p class="font-weight-bold mb-0">
                                            <?= htmlspecialchars($kelas); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h6 class="text-muted">Sekolah</h6>
                                        <p class="font-weight-bold mb-0">
                                            <?= htmlspecialchars($sekolah); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h6 class="text-muted">Jurusan</h6>
                                        <p class="font-weight-bold mb-0">
                                            <?= htmlspecialchars($jurusan); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h6 class="text-muted">Hobi</h6>
                                        <p class="font-weight-bold mb-0">
                                            <?= htmlspecialchars($hobi); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-primary">
                                    <div class="card-body">
                                        <h6 class="text-muted">Cita-cita</h6>
                                        <p class="font-weight-bold mb-0">
                                            <?= htmlspecialchars($cita_cita); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- DESKRIPSI -->
                        <h5 class="text-primary font-weight-bold mt-4 mb-3">
                            Deskripsi Saya
                        </h5>

                        <div class="alert alert-primary" role="alert">
                            Saya adalah seorang pelajar yang sedang belajar
                            Rekayasa Perangkat Lunak. Saya senang mempelajari
                            teknologi dan membuat website. Saya ingin terus belajar
                            agar dapat mengembangkan kemampuan dan meraih cita-cita.
                        </div>

                        <!-- TOMBOL -->
                        <div class="text-center mt-4">
                            <a href="dashboard.php" class="btn btn-primary btn-lg">
                                &larr; Kembali ke Dashboard
                            </a>
                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="card-footer text-center text-muted">
                        <?= date('Y'); ?>
                        <?= htmlspecialchars($nama); ?>
                    </div>

                </div>

            </div>

        </main>

    </div>
</div>

<!-- JavaScript Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
