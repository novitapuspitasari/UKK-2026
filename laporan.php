<?php
// laporan.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

// ========================================
// AMBIL DATA SESSION
// ========================================

$role = $_SESSION['role'] ?? '';
$id_user = (int) ($_SESSION['id'] ?? $_SESSION['id_user'] ?? 0);

// ========================================
// CEK HAK AKSES
// ========================================

if ($role !== 'admin' && $role !== 'guru') {
    http_response_code(403);
    exit('Akses ditolak. Halaman ini hanya dapat diakses oleh Admin dan Guru Wali Kelas.');
}

// ========================================
// CEK GURU WALI KELAS AKTIF
// ========================================

$id_guru = 0;

if ($role === 'guru') {

    $stmt_guru = mysqli_prepare(
        $koneksi,
        "SELECT g.id
         FROM t_guru g
         INNER JOIN t_wali_kelas wk ON wk.guru_id = g.id
         WHERE g.user_id = ?
         AND wk.status_aktif = 1
         LIMIT 1"
    );

    if (!$stmt_guru) {
        die("Gagal mengambil data guru: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt_guru, "i", $id_user);
    mysqli_stmt_execute($stmt_guru);

    $hasil_guru = mysqli_stmt_get_result($stmt_guru);
    $data_guru = mysqli_fetch_assoc($hasil_guru);

    mysqli_stmt_close($stmt_guru);

    if (!$data_guru) {
        http_response_code(403);
        exit('Akses ditolak. Anda bukan Guru Wali Kelas yang aktif.');
    }

    $id_guru = (int) $data_guru['id'];
}

// ========================================
// FILTER TANGGAL
// ========================================

$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Validasi format tanggal
if (
    ($tanggal_mulai !== '' &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_mulai)) ||

    ($tanggal_selesai !== '' &&
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal_selesai))
) {
    exit('Format tanggal tidak valid.');
}

// Validasi tanggal sebenarnya
foreach ([$tanggal_mulai, $tanggal_selesai] as $tanggal) {

    if ($tanggal !== '') {

        $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

        if (!$date || $date->format('Y-m-d') !== $tanggal) {
            exit('Tanggal tidak valid.');
        }
    }
}

// Validasi tanggal mulai dan selesai
if (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    exit('Tanggal mulai tidak boleh melebihi tanggal selesai.');
}

// ========================================
// FORMAT TANGGAL INDONESIA
// ========================================

function formatTanggalIndonesia($tanggal)
{
    if ($tanggal === '') {
        return 'Semua Tanggal';
    }

    $bulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    $timestamp = strtotime($tanggal);

    if (!$timestamp) {
        return '-';
    }

    return date('d', $timestamp) . ' ' .
        $bulan[(int) date('n', $timestamp)] . ' ' .
        date('Y', $timestamp);
}

$periode_mulai = formatTanggalIndonesia($tanggal_mulai);
$periode_selesai = formatTanggalIndonesia($tanggal_selesai);

$tanggal_cetak = formatTanggalIndonesia(date('Y-m-d'));

// ========================================
// QUERY LAPORAN
// ========================================

$sql = "SELECT
            ps.id,
            ps.tahun_ajaran_id,
            ps.siswa_id,
            ps.nama_siswa,
            ps.kelas_id,
            ps.nama_kelas,
            ps.pelanggaran_id,
            ps.nama_pelanggaran,
            ps.pelanggaran_kategori_id,
            ps.guru_id,
            ps.nama_guru,
            ps.tanggal,
            ps.keterangan,
            ps.poin,
            ps.tindakan,
            ps.status

        FROM t_pelanggaran_siswa ps

        WHERE 1 = 1";

$types = "";
$params = [];

// ========================================
// FILTER HAK AKSES GURU
// ========================================

if ($role === 'guru') {

    $sql .= " AND EXISTS (

                SELECT 1
                FROM t_wali_kelas wk

                WHERE wk.guru_id = ?
                AND wk.kelas_id = ps.kelas_id
                AND wk.tahun_ajaran_id = ps.tahun_ajaran_id
                AND wk.status_aktif = 1

            )";

    $types .= "i";
    $params[] = $id_guru;
}

// ========================================
// FILTER TANGGAL MULAI
// ========================================

if ($tanggal_mulai !== '') {

    $sql .= " AND ps.tanggal >= ?";

    $types .= "s";
    $params[] = $tanggal_mulai;
}

// ========================================
// FILTER TANGGAL SELESAI
// ========================================

if ($tanggal_selesai !== '') {

    $sql .= " AND ps.tanggal <= ?";

    $types .= "s";
    $params[] = $tanggal_selesai;
}

// Urutkan berdasarkan tanggal terbaru
$sql .= " ORDER BY ps.tanggal DESC, ps.id DESC";

// ========================================
// EKSEKUSI QUERY
// ========================================

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan laporan: " . htmlspecialchars(mysqli_error($koneksi)));
}

// Bind parameter
if (!empty($params)) {

    $bind_params = [$stmt, $types];

    foreach ($params as $key => $value) {
        $bind_params[] = &$params[$key];
    }

    call_user_func_array('mysqli_stmt_bind_param', $bind_params);
}

// Jalankan query
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);

if (!$hasil) {
    die("Gagal mengambil data laporan: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <!-- BOOTSTRAP 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- DATATABLES -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css"
        rel="stylesheet"
    >

    <style>
        @media print {
            .sidebar-print-hide {
                display: none !important;
            }

            main {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }

            .container-fluid,
            .container-fluid > .row {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 7px;
        }

        .card-header {
            font-weight: 500;
        }

        .form-label {
            font-weight: 500;
        }

        table th,
        table td {
            vertical-align: middle;
        }

        /* BAGIAN KHUSUS CETAK */
        .print-only {
            display: none;
        }

        /* =====================================
           PENGATURAN DOKUMEN CETAK
        ===================================== */

        @media print {

            @page {
                size: A4 landscape;
                margin: 15mm;
            }

            body {
                background: white !important;
                color: black !important;
                font-family: "Times New Roman", serif !important;
                font-size: 11pt;
            }

            /* Sembunyikan elemen website */
            .no-print,
            .sidebar,
            #tabelLaporan_wrapper .dt-search,
            #tabelLaporan_wrapper .dt-length,
            #tabelLaporan_wrapper .dt-paging,
            #tabelLaporan_wrapper .dt-info {
                display: none !important;
            }

            /* Tampilkan kop dan tanda tangan */
            .print-only {
                display: block !important;
            }

            /* Konten memenuhi halaman */
            .container-fluid,
            .row,
            main {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            main {
                flex: 0 0 100% !important;
            }

            /* Sembunyikan judul website */
            main > h1,
            main > p {
                display: none !important;
            }

            /* Hilangkan card */
            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-header {
                display: none !important;
            }

            .card-body {
                padding: 0 !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            /* Tabel dokumen */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-family: "Times New Roman", serif !important;
                font-size: 10pt !important;
            }

            table th,
            table td {
                border: 1px solid black !important;
                padding: 6px !important;
                color: black !important;
                background: white !important;
                vertical-align: middle !important;
            }

            thead {
                display: table-header-group;
            }

            tr {
                page-break-inside: avoid;
            }

            /* Kop surat */
            .kop-surat {
                text-align: center;
                line-height: 1.3;
            }

            .kop-surat h4 {
                font-size: 16pt;
                font-weight: bold;
                margin-bottom: 3px;
            }

            .kop-surat p {
                font-size: 10pt;
                margin-bottom: 3px;
            }

            .kop-surat hr {
                border: none;
                border-top: 3px solid black;
                margin-top: 10px;
                margin-bottom: 15px;
                opacity: 1;
            }

            .judul-laporan {
                font-size: 14pt;
                font-weight: bold;
                text-align: center;
                margin-bottom: 8px;
            }

            .informasi-laporan {
                font-size: 10pt;
                text-align: center;
                line-height: 1.5;
                margin-bottom: 20px;
            }

            /* Tanda tangan */
            .tanda-tangan {
                margin-top: 35px !important;
                page-break-inside: avoid;
            }

            .tanda-tangan p {
                font-size: 11pt;
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
        <main class="col-md-9 col-lg-10 p-4">

            <!-- JUDUL HALAMAN -->
            <h1 class="mb-3">
                Laporan Pelanggaran Siswa
            </h1>

            <p class="text-muted">
                Halaman laporan data pelanggaran siswa.
            </p>


            <!-- INFORMASI AKSES -->
            <div class="alert alert-info no-print">

                <?php if ($role === 'admin') { ?>

                    Anda login sebagai <strong>Admin</strong>.
                    Anda dapat melihat seluruh laporan pelanggaran siswa.

                <?php } else { ?>

                    Anda login sebagai <strong>Guru Wali Kelas</strong>.
                    Anda hanya dapat melihat laporan dari kelas yang menjadi tanggung jawab Anda.

                <?php } ?>

            </div>


            <!-- =====================================
                 FILTER LAPORAN
            ====================================== -->

            <div class="card shadow-sm mb-4 no-print">

                <div class="card-header bg-primary text-white">
                    Filter Laporan
                </div>

                <div class="card-body">

                    <form method="GET" action="laporan.php">

                        <div class="row align-items-end">

                            <!-- TANGGAL MULAI -->
                            <div class="col-md-4 mb-3">

                                <label
                                    for="tanggal_mulai"
                                    class="form-label"
                                >
                                    Tanggal Mulai
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_mulai"
                                    id="tanggal_mulai"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggal_mulai); ?>"
                                >

                            </div>


                            <!-- TANGGAL SELESAI -->
                            <div class="col-md-4 mb-3">

                                <label
                                    for="tanggal_selesai"
                                    class="form-label"
                                >
                                    Tanggal Selesai
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_selesai"
                                    id="tanggal_selesai"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggal_selesai); ?>"
                                >

                            </div>


                            <!-- TOMBOL FILTER -->
                            <div class="col-md-4 mb-3">

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    Tampilkan
                                </button>

                                <a
                                    href="laporan.php"
                                    class="btn btn-danger"
                                >
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- =====================================
                 TABEL LAPORAN
            ====================================== -->

            <div class="card shadow-sm">

                <!-- HEADER TABEL -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <span>
                        Data Laporan Pelanggaran
                    </span>

                    <button
                        type="button"
                        onclick="cetakLaporan()"
                        class="btn btn-light btn-sm no-print"
                    >
                        Cetak Laporan
                    </button>

                </div>


                <!-- BODY TABEL -->
                <div class="card-body">


                    <!-- =====================================
                         KOP SURAT KHUSUS CETAK
                    ====================================== -->

                    <div class="print-only kop-surat">

                        <h4>
                            SMK MUHAMMADIYAH TASIKMALAYA
                        </h4>

                        <p>
                            Alamat: ........................................................
                        </p>

                        <hr>

                        <div class="judul-laporan">
                            LAPORAN PELANGGARAN SISWA
                        </div>

                        <div class="informasi-laporan">

                            Periode:
                            <?= htmlspecialchars($periode_mulai); ?>
                            s.d.
                            <?= htmlspecialchars($periode_selesai); ?>

                            <br>

                            Tanggal Cetak:
                            <?= htmlspecialchars($tanggal_cetak); ?>

                        </div>

                    </div>


                    <!-- =====================================
                         TABEL DATA
                    ====================================== -->

                    <div class="table-responsive">

                        <table
                            id="tabelLaporan"
                            class="table table-striped table-bordered table-hover align-middle w-100"
                        >

                            <thead class="table-dark">

                                <tr>

                                    <th>No</th>

                                    <th>Nama Siswa</th>

                                    <th>Kelas</th>

                                    <th>Nama Pelanggaran</th>

                                    <th>Guru Pencatat</th>

                                    <th>Tanggal</th>

                                    <th>Poin</th>

                                    <th>Tindakan</th>

                                    <th>Status</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                $no = 1;

                                while ($row = mysqli_fetch_assoc($hasil)) {

                                ?>

                                    <tr>

                                        <!-- NOMOR -->
                                        <td>
                                            <?= $no++; ?>
                                        </td>


                                        <!-- NAMA SISWA -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?>
                                        </td>


                                        <!-- KELAS -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                        </td>


                                        <!-- PELANGGARAN -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?>
                                        </td>


                                        <!-- GURU PENCATAT -->
                                        <td>
                                            <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                        </td>


                                        <!-- TANGGAL -->
                                        <td>
                                            <?= htmlspecialchars($row['tanggal'] ?? '-'); ?>
                                        </td>


                                        <!-- POIN -->
                                        <td>
                                            <?= (int) $row['poin']; ?>
                                        </td>


                                        <!-- TINDAKAN -->
                                        <td>
                                            <?= htmlspecialchars($row['tindakan'] ?? '-'); ?>
                                        </td>


                                        <!-- STATUS -->
                                        <td>
                                            <?= htmlspecialchars($row['status'] ?? '-'); ?>
                                        </td>

                                    </tr>

                                <?php } ?>

                            </tbody>

                        </table>

                    </div>


                    <!-- =====================================
                         TANDA TANGAN KHUSUS CETAK
                    ====================================== -->

                    <div class="print-only tanda-tangan">

                        <div class="row">

                            <!-- KEPALA SEKOLAH -->
                            <div class="col-6 text-center">

                                <p class="mb-1">
                                    Mengetahui,
                                </p>

                                <p class="mb-5">
                                    Kepala Sekolah
                                </p>

                                <p class="fw-bold mb-0">
                                    __________________________
                                </p>

                                <p class="mb-0">
                                    NIP. ................................
                                </p>

                            </div>


                            <!-- GURU WALI KELAS -->
                            <div class="col-6 text-center">

                                <p class="mb-1">
                                    Tasikmalaya,
                                    <?= htmlspecialchars($tanggal_cetak); ?>
                                </p>

                                <p class="mb-5">
                                    Guru Wali Kelas
                                </p>

                                <p class="fw-bold mb-0">
                                    __________________________
                                </p>

                                <p class="mb-0">
                                    NIP. ................................
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================
                 TOMBOL KEMBALI
            ====================================== -->

            <div class="mt-3 no-print">

                <a
                    href="dashboard.php"
                    class="btn btn-primary"
                >
                    Kembali ke Dashboard
                </a>

            </div>

        </main>

    </div>

</div>


<!-- =====================================
     JAVASCRIPT
====================================== -->

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DATATABLES -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<!-- DATATABLES BOOTSTRAP 5 -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>


<script>

// ========================================
// INISIALISASI DATATABLES
// ========================================

let tabelLaporan;

let panjangHalamanSebelumnya = 10;

$(document).ready(function () {

    tabelLaporan = $('#tabelLaporan').DataTable({

        language: {

            search: "Cari:",

            lengthMenu: "Tampilkan _MENU_ data",

            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",

            infoEmpty: "Tidak ada data laporan",

            infoFiltered: "(difilter dari _MAX_ total data)",

            zeroRecords: "Data laporan tidak ditemukan",

            emptyTable: "Belum ada data pelanggaran",

            paginate: {

                first: "Pertama",

                last: "Terakhir",

                next: "Berikutnya",

                previous: "Sebelumnya"

            }

        },

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [[5, 'desc']],

        columnDefs: [

            {

                targets: 0,

                orderable: false,

                searchable: false

            }

        ]

    });

});


// ========================================
// CETAK SELURUH DATA LAPORAN
// ========================================

function cetakLaporan() {

    // Simpan jumlah data per halaman
    panjangHalamanSebelumnya = tabelLaporan.page.len();

    // Tampilkan seluruh data
    tabelLaporan.page.len(-1).draw();

    // Cetak dokumen
    window.print();

}


// ========================================
// KEMBALIKAN PAGINATION SETELAH CETAK
// ========================================

window.addEventListener('afterprint', function () {

    tabelLaporan.page.len(panjangHalamanSebelumnya).draw();

});

</script>

</body>

</html>

<?php

// ========================================
// TUTUP STATEMENT
// ========================================

mysqli_stmt_close($stmt);

?>