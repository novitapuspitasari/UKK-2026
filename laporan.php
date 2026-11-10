<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil data session
$role = $_SESSION['role'] ?? '';
$id_user = (int) ($_SESSION['id_user'] ?? 0);

// Cek hak akses
if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    exit('Akses ditolak.');
}

// Cek guru wali kelas
$id_guru = 0;

if ($role === 'guru') {
    $stmtGuru = mysqli_prepare(
        $koneksi,
        "SELECT g.id
         FROM t_guru g
         INNER JOIN t_wali_kelas wk ON wk.guru_id = g.id
         WHERE g.user_id = ?
         AND wk.status_aktif = 1
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmtGuru, "i", $id_user);
    mysqli_stmt_execute($stmtGuru);

    $hasilGuru = mysqli_stmt_get_result($stmtGuru);
    $dataGuru = mysqli_fetch_assoc($hasilGuru);

    mysqli_stmt_close($stmtGuru);

    if (!$dataGuru) {
        http_response_code(403);
        exit('Akses ditolak. Anda bukan Guru Wali Kelas aktif.');
    }

    $id_guru = (int) $dataGuru['id'];
}

// Filter tanggal
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

if (
    ($tanggal_mulai !== '' &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai)) ||
    ($tanggal_selesai !== '' &&
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai))
) {
    exit('Format tanggal tidak valid.');
}

if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    exit('Tanggal mulai tidak boleh melebihi tanggal selesai.');
}

// Query laporan
$sql = "
    SELECT
        ps.id,
        s.nis,
        s.nama AS nama_siswa,
        p.kode,
        p.nama AS nama_pelanggaran,
        p.poin
    FROM t_pelanggaran_siswa ps
    JOIN t_siswa s ON ps.siswa_id = s.id
    JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
    WHERE 1 = 1
";

$types = "";
$params = [];

// Guru hanya melihat kelas tanggung jawabnya
if ($role === 'guru') {
    $sql .= "
        AND EXISTS (
            SELECT 1
            FROM t_wali_kelas wk
            WHERE wk.guru_id = ?
            AND wk.kelas_id = ps.kelas_id
            AND wk.tahun_ajaran_id = ps.tahun_ajaran_id
            AND wk.status_aktif = 1
        )
    ";

    $types .= "i";
    $params[] = $id_guru;
}

// Filter tanggal mulai
if ($tanggal_mulai !== '') {
    $sql .= " AND ps.tanggal >= ?";
    $types .= "s";
    $params[] = $tanggal_mulai;
}

// Filter tanggal selesai
if ($tanggal_selesai !== '') {
    $sql .= " AND ps.tanggal <= ?";
    $types .= "s";
    $params[] = $tanggal_selesai;
}

$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// Jalankan query
$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal mengambil laporan: " . mysqli_error($koneksi));
}

if (!empty($params)) {
    $bind = [$stmt, $types];

    foreach ($params as $key => $value) {
        $bind[] = &$params[$key];
    }

    call_user_func_array('mysqli_stmt_bind_param', $bind);
}

mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

// Export Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {

    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_pelanggaran.xls');

    echo "\xEF\xBB\xBF";
    echo '<table border="1">';
    echo '<tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Kode Pelanggaran</th>
            <th>Jenis Pelanggaran</th>
            <th>Poin</th>
          </tr>';

    $no = 1;

    while ($row = mysqli_fetch_assoc($hasil)) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['nis']) . '</td>';
        echo '<td>' . htmlspecialchars($row['nama_siswa']) . '</td>';
        echo '<td>' . htmlspecialchars($row['kode'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['nama_pelanggaran']) . '</td>';
        echo '<td>' . (int) $row['poin'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- DataTables -->
    <link rel="stylesheet"
    href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

    <style>
        body {
            background-color: #f8f8f8;
            font-family: Arial, sans-serif;
        }

        main {
            min-height: 100vh;
            background-color: #f8f8f8;
        }

        .judul {
            font-size: 26px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        .deskripsi {
            font-size: 17px;
            color: #111;
        }

        .btn-dashboard {
            background-color: #007bff;
            color: white;
            padding: 8px 18px;
            border-radius: 5px;
            margin-bottom: 12px;
        }

        .btn-dashboard:hover {
            background-color: #0069d9;
            color: white;
        }

        .btn-cetak {
            background-color: #dc3545;
            color: white;
            font-size: 10px;
            padding: 5px 10px;
        }

        .btn-excel {
            background-color: #28a745;
            color: white;
            font-size: 10px;
            padding: 5px 10px;
        }

        .table {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .table thead th {
            background-color: #111;
            color: white;
            padding: 7px 5px;
            vertical-align: middle;
        }

        .table tbody td {
            padding: 7px 5px;
            vertical-align: middle;
        }

        .dataTables_wrapper {
            font-size: 11px;
        }

        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 10px;
        }

        .dataTables_info {
            padding-top: 8px !important;
        }

        .dataTables_paginate {
            margin-top: 5px;
        }

        .dataTables_filter input {
            height: 23px;
            width: 85px !important;
        }

        .dataTables_length select {
            height: 32px;
            width: 55px !important;
        }

        @media print {
            .sidebar,
            .btn-dashboard,
            .tombol-aksi,
            .dataTables_length,
            .dataTables_filter,
            .dataTables_info,
            .dataTables_paginate {
                display: none !important;
            }

            main {
                width: 100%;
                max-width: 100%;
            }

            .table {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-12 col-md-9 col-lg-10 p-3">

            <div class="row align-items-center mb-3">

                <div class="col-md-8">
                    <h3 class="judul">
                        Laporan Pelanggaran Siswa
                    </h3>

                    <p class="deskripsi">
                        Selamat datang di halaman Laporan Pelanggaran Siswa
                    </p>
                </div>

                <div class="col-md-4 text-md-right tombol-aksi">

                    <button onclick="cetakPDF()"
                        class="btn btn-cetak">
                        Cetak PDF
                    </button>

                    <a href="laporan.php?export=excel"
                        class="btn btn-excel">
                        Export Excel
                    </a>

                </div>

            </div>

            <!-- Tombol Dashboard -->
            <div class="mb-2">
                <a href="dashboard.php" class="btn btn-dashboard">
                    Kembali ke Dashboard
                </a>
            </div>
            

            <!-- TABEL LAPORAN -->
            <div class="table-responsive">

                <table id="tabelLaporan"
                    class="table table-bordered table-striped table-hover w-100">

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kode Pelanggaran</th>
                            <th>Jenis Pelanggaran</th>
                            <th>Poin</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        while ($row = mysqli_fetch_assoc($hasil)) {
                        ?>

                        <tr>
                            <td><?= $no++; ?></td>

                            <td>
                                <?= htmlspecialchars($row['nis']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_siswa']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['kode'] ?? ''); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['nama_pelanggaran']); ?>
                            </td>

                            <td>
                                <?= (int) $row['poin']; ?>
                            </td>
                        </tr>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </main>

    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>

$(document).ready(function () {

    $('#tabelLaporan').DataTable({

        language: {
            search: "Cari :",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada laporan pelanggaran",

            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },

        pageLength: 5,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[2, 'asc']],

        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]

    });

});

// Cetak PDF
function cetakPDF() {

    window.print();

}

</script>

</body>
</html>