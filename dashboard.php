
<?php
include "includes/cek_session.php";
include "config/koneksi.php";

$nama = $_SESSION['nama'] ?? $_SESSION['nama_lengkap'] ?? '';
$role = strtolower($_SESSION['role'] ?? '-');

function hitungData($koneksi, $tabel) {
    $hasil = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $tabel");
    return $hasil ? (int) mysqli_fetch_assoc($hasil)['total'] : 0;
}

$total_siswa = hitungData($koneksi, 't_siswa');
$total_guru = hitungData($koneksi, 't_guru');
$total_kelas = hitungData($koneksi, 't_kelas');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <?php include "layout/sidebar.php"; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="fw-bold">Dashboard</h2>
            <p class="text-secondary">
                Selamat datang, <b><?= htmlspecialchars($nama); ?></b>!
            </p>

            <?php if ($role == 'admin') { ?>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5>🎓 Data Siswa</h5>
                                <h2 class="text-primary"><?= $total_siswa; ?></h2>
                                <a href="kelola_siswa.php" class="btn btn-primary btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5>👨‍🏫 Data Guru</h5>
                                <h2 class="text-success"><?= $total_guru; ?></h2>
                                <a href="kelola_guru.php" class="btn btn-success btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h5>🏫 Data Kelas</h5>
                                <h2 class="text-warning"><?= $total_kelas; ?></h2>
                                <a href="kelola_kelas.php" class="btn btn-warning btn-sm">
                                    Lihat Data
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            <?php } elseif ($role == 'guru') { ?>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5>📝 Catat Pelanggaran</h5>
                                <p>Catat pelanggaran siswa.</p>
                                <a href="catat_pelanggaran.php" class="btn btn-primary btn-sm">
                                    Catat Sekarang
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5>📋 Riwayat Pelanggaran</h5>
                                <p>Lihat riwayat pelanggaran siswa.</p>
                                <a href="riwayat.php" class="btn btn-success btn-sm">
                                    Lihat Riwayat
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body">
                                <h5>📊 Rekap Poin</h5>
                                <p>Lihat poin pelanggaran siswa.</p>
                                <a href="rekap_poin.php" class="btn btn-warning btn-sm">
                                    Lihat Rekap
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            <?php } else { ?>

                <div class="alert alert-danger">
                    Role pengguna tidak dikenali.
                </div>

            <?php } ?>

        </main>
    </div>
</div>

</body>
</html>