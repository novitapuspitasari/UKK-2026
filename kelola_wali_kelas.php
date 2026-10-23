
<?php
// kelola_wali_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT
            w.*,
            k.nama AS nama_kelas,
            g.nama AS nama_guru
        FROM t_wali_kelas w
        LEFT JOIN t_kelas k ON w.kelas_id = k.id
        LEFT JOIN t_guru g ON w.guru_id = g.id
        ORDER BY g.nama ASC";

$hasil = mysqli_query($koneksi, $sql);

if (!$hasil) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Wali Kelas - Sistem Pelanggaran Siswa</title>

    <!-- Bootstrap 4.6.2 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- DataTables Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <!-- JUDUL -->
            <div class="mb-4">
                <h1 class="text-primary font-weight-bold">
                    Kelola Wali Kelas
                </h1>

                <p class="text-muted">
                    Kelola data guru dan kelas yang menjadi wali kelas.
                </p>
            </div>

            <!-- TOMBOL -->
            <div class="mb-3">
                <a href="dashboard.php" class="btn btn-secondary mr-2 mb-2">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_wali_kelas.php" class="btn btn-primary mb-2">
                    + Tambah Wali Kelas
                </a>
            </div>

            <!-- CARD TABEL -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Daftar Wali Kelas</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table id="tabelWaliKelas"
                               class="table table-bordered table-striped table-hover">

                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Guru</th>
                                    <th>Kelas</th>
                                    <th>Status Aktif</th>
                                    <th>Dibuat</th>
                                    <th>Diubah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
                                <tr>
                                    <!-- Nomor otomatis -->
                                    <td class="text-center"></td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if (
                                            isset($row['status_aktif']) &&
                                            (int)$row['status_aktif'] === 1
                                        ) { ?>
                                            <span class="badge badge-success">
                                                Aktif
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge badge-secondary">
                                                Tidak Aktif
                                            </span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['created_at'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['updated_at'] ?? ''); ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="edit_wali_kelas.php?id=<?= urlencode((string)$row['id']); ?>"
                                           class="btn btn-info btn-sm mb-1">
                                            Edit
                                        </a>

                                        <a href="hapus_wali_kelas.php?id=<?= urlencode((string)$row['id']); ?>"
                                           class="btn btn-danger btn-sm mb-1"
                                           onclick="return confirm('Yakin ingin menghapus data wali kelas ini?');">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>

                </div>

                <div class="card-footer text-muted">
                    Sistem Pelanggaran Siswa
                </div>
            </div>

        </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelWaliKelas').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,

        // Urutan awal berdasarkan nama guru
        order: [[1, 'asc']],

        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data wali kelas tidak ditemukan",
            emptyTable: "Belum ada data wali kelas",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            search: "Cari wali kelas:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },

        // Kolom nomor tidak ikut diurutkan
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        // Nomor mengikuti urutan dan halaman yang tampil
        drawCallback: function () {
            const api = this.api();
            const info = api.page.info();

            api.column(0, { page: 'current' })
                .nodes()
                .each(function (cell, i) {
                    cell.innerHTML = info.start + i + 1;
                });
        }
    });
});
</script>

</body>
</html>