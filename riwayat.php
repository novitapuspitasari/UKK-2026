
<?php
// riwayat.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// AMBIL DATA SESSION
// ========================================

$role = strtolower(trim($_SESSION['role'] ?? ''));
$id_user = (int) ($_SESSION['id'] ?? $_SESSION['id_user'] ?? 0);

// ========================================
// CEK GURU YANG SEDANG LOGIN
// ========================================

$guru_id = 0;

if ($role == 'guru' || $role == 'wali kelas' || $role == 'wali_kelas') {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_guru WHERE user_id = ? AND status_aktif = 1 LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);

    if ($data_guru = mysqli_fetch_assoc($hasil_guru)) {
        $guru_id = (int) $data_guru['id'];
    }

    mysqli_stmt_close($stmt_guru);
}

// ========================================
// PENCARIAN DAN FILTER
// ========================================

$cari = isset($_GET['cari'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['cari']))
    : '';

$status = isset($_GET['status'])
    ? mysqli_real_escape_string($koneksi, trim($_GET['status']))
    : '';

// ========================================
// QUERY RIWAYAT
// ========================================

$sql = "SELECT *
        FROM t_pelanggaran_siswa
        WHERE 1=1";

// Jika login sebagai guru/wali kelas,
// tampilkan hanya pelanggaran yang dicatat sendiri.

if ($role == 'guru' || $role == 'wali kelas' || $role == 'wali_kelas') {

    if ($guru_id > 0) {
        $sql .= " AND guru_id = $guru_id";
    } else {
        // Jika guru tidak ditemukan, jangan tampilkan data guru lain.
        $sql .= " AND 1=0";
    }
}

// Pencarian
if ($cari != '') {
    $sql .= " AND (
        nama_siswa LIKE '%$cari%'
        OR nama_pelanggaran LIKE '%$cari%'
        OR nama_kelas LIKE '%$cari%'
        OR nama_guru LIKE '%$cari%'
    )";
}

// Filter status
if ($status != '') {
    $sql .= " AND status = '$status'";
}

$sql .= " ORDER BY tanggal DESC, id DESC";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Gagal mengambil data: ' . mysqli_error($koneksi));
}

$jumlah = mysqli_num_rows($query);
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
            font-family: Arial, sans-serif;
        }

        main {
            min-height: 100vh;
        }

        h2 {
            color: #2c4968;
            font-size: 25px;
            font-weight: 600;
        }

        .card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 14px 18px;
            border-bottom: 1px solid #dce6f1;
        }

        .card-header h5 {
            font-size: 16px;
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .form-label {
            font-size: 14px;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            font-size: 14px;
        }

        .btn {
            font-size: 14px;
        }

        .btn-primary {
            background-color: #3978b8;
            border-color: #3978b8;
        }

        .btn-primary:hover {
            background-color: #2c6197;
            border-color: #2c6197;
        }

        #tabelRiwayat {
            font-size: 13px;
            vertical-align: middle;
        }

        #tabelRiwayat td,
        #tabelRiwayat th {
            padding: 11px;
        }

        .dataTables_wrapper {
            font-size: 13px;
        }

        .badge {
            font-weight: 500;
        }

        @media (max-width: 768px) {
            main {
                padding: 16px !important;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <!-- Judul halaman -->
            <div class="d-flex justify-content-between align-items-center
                        flex-wrap gap-2 mb-4">

                <div>
                    <h2>Riwayat Pelanggaran Siswa</h2>
                    <p class="text-muted mb-0">
                        Daftar catatan pelanggaran siswa.
                    </p>
                </div>

                <a href="dashboard.php" class="btn btn-outline-primary">
                    Kembali
                </a>

            </div>

            <!-- Jumlah data -->
            <div class="card">
                <div class="card-body">

                    <div class="text-muted small">
                        Jumlah Riwayat Ditampilkan
                    </div>

                    <h4 class="text-primary mt-2 mb-0">
                        <?= $jumlah; ?> data
                    </h4>

                </div>
            </div>

            <!-- Pencarian dan Filter -->
            <div class="card">

                <div class="card-header bg-primary text-white">
                    <h5 class="text-white mb-0">
                        Pencarian dan Filter
                    </h5>
                </div>

                <div class="card-body">

                    <form method="GET" action="riwayat.php">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="cari" class="form-label">
                                    Cari Riwayat
                                </label>

                                <input
                                    type="text"
                                    name="cari"
                                    id="cari"
                                    class="form-control"
                                    placeholder="Nama siswa, pelanggaran, kelas, guru..."
                                    value="<?= htmlspecialchars($cari); ?>">
                            </div>

                            <div class="col-md-3">
                                <label for="status" class="form-label">
                                    Status
                                </label>

                                <select name="status" id="status"
                                        class="form-select">

                                    <option value="">Semua Status</option>

                                    <option value="belum"
                                        <?= $status == 'belum' ? 'selected' : ''; ?>>
                                        Belum Ditindak
                                    </option>

                                    <option value="selesai"
                                        <?= $status == 'selesai' ? 'selected' : ''; ?>>
                                        Selesai
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end gap-2">

                                <button type="submit" class="btn btn-primary">
                                    Cari
                                </button>

                                <a href="riwayat.php" class="btn btn-danger">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>
            </div>

            <!-- Data Riwayat Pelanggaran -->
            <div class="card">

                <div class="card-header bg-primary text-white">
                    <h5 class="text-white mb-0">
                        Data Riwayat Pelanggaran
                    </h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table id="tabelRiwayat"
                               class="table table-bordered table-striped table-hover">

                            <!-- Header tabel hitam -->
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pelanggaran</th>
                                    <th>Poin</th>
                                    <th>Guru Pencatat</th>
                                    <th>Keterangan</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php while ($data = mysqli_fetch_assoc($query)) { ?>

                                    <tr>
                                        <td></td>

                                        <td>
                                            <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                        </td>

                                        <td class="text-center">
                                            <?= htmlspecialchars((string) ($data['poin'] ?? 0)); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['tindakan'] ?? '-'); ?>
                                        </td>

                                        <td>
                                            <?php
                                            $status_data = strtolower(
                                                trim($data['status'] ?? '')
                                            );

                                            if ($status_data == 'selesai') {
                                                echo '<span class="badge bg-success">Selesai</span>';
                                            } elseif (
                                                $status_data == 'belum' ||
                                                $status_data == 'belum ditindak'
                                            ) {
                                                echo '<span class="badge bg-danger">Belum Ditindak</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">'
                                                    . htmlspecialchars($data['status'] ?? '-')
                                                    . '</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    $('#tabelRiwayat').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[1, 'desc']],

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        drawCallback: function () {

            var api = this.api();
            var nomorAwal = api.page.info().start;

            api.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {

                cell.innerHTML = nomorAwal + i + 1;

            });

        },

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data",

            infoFiltered: "(difilter dari _MAX_ data)",

            zeroRecords: "Data tidak ditemukan",

            emptyTable: "Belum ada data riwayat",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }

        }

    });

});
</script>

</body>
</html>