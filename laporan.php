
<?php
// laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// =====================================
// EXPORT EXCEL
// =====================================
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $sqlExcel = "
        SELECT
            s.nis,
            s.nama AS nama_siswa,
            p.kode,
            p.nama AS nama_pelanggaran,
            p.poin
        FROM t_pelanggaran_siswa ps
        JOIN t_siswa s ON ps.siswa_id = s.id
        JOIN t_pelanggaran p ON ps.pelanggaran_id = p.id
        ORDER BY ps.id DESC
    ";

    $dataExcel = mysqli_query($koneksi, $sqlExcel);

    if (!$dataExcel) {
        die('Gagal mengambil data laporan.');
    }

    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan_pelanggaran.xls');
    header('Pragma: no-cache');
    header('Expires: 0');

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

    while ($row = mysqli_fetch_assoc($dataExcel)) {
        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars((string) $row['nis'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars((string) $row['nama_siswa'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars((string) ($row['kode'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars((string) $row['nama_pelanggaran'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . (int) $row['poin'] . '</td>';
        echo '</tr>';
    }

    echo '</table>';
    mysqli_free_result($dataExcel);
    exit;
}

// =====================================
// AMBIL DATA LAPORAN
// =====================================
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
    ORDER BY ps.id DESC
";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die('Gagal mengambil laporan: ' .
        htmlspecialchars(mysqli_error($koneksi), ENT_QUOTES, 'UTF-8'));
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

    <!-- DataTables Bootstrap -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-12 col-md-9 col-lg-10 p-3 p-md-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <h3 class="mb-2">Laporan Pelanggaran Siswa</h3>

                <div class="mb-2">
                    <button type="button"
                            class="btn btn-danger btn-sm mr-1"
                            onclick="cetakPDF()">
                        Cetak PDF
                    </button>

                    <a href="laporan.php?export=excel"
                       class="btn btn-success btn-sm">
                        Export Excel
                    </a>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Data Laporan Pelanggaran</h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <a href="dashboard.php" class="btn btn-secondary btn-sm">
                            Kembali ke Dashboard
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="tabelLaporan"
                               class="table table-bordered table-striped table-hover w-100">

                            <thead class="thead-dark">
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
                                            <?= htmlspecialchars(
                                                (string) $row['nis'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                (string) $row['nama_siswa'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                (string) ($row['kode'] ?? ''),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                (string) $row['nama_pelanggaran'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ); ?>
                                        </td>

                                        <td><?= (int) $row['poin']; ?></td>
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

<!-- JavaScript Bootstrap -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
let panjangHalamanSebelumCetak = 10;

$(document).ready(function () {
    $('#tabelLaporan').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada laporan pelanggaran",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
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

// Cetak atau simpan laporan sebagai PDF
function cetakPDF() {
    const tabel = $('#tabelLaporan').DataTable();

    panjangHalamanSebelumCetak = tabel.page.len();
    tabel.page.len(-1).draw();

    setTimeout(function () {
        window.print();
    }, 300);
}

// Kembalikan pagination setelah proses cetak
window.addEventListener('afterprint', function () {
    const tabel = $('#tabelLaporan').DataTable();
    tabel.page.len(panjangHalamanSebelumCetak).draw();
});
</script>

</body>
</html>