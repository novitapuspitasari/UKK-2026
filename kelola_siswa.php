
<?php
// kelola_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_siswa ORDER BY nama ASC";
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

    <title>Kelola Siswa - Sistem Pelanggaran Siswa</title>

    <!-- Bootstrap 4.6.2 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- DataTables Bootstrap 4 -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h1>Kelola Siswa</h1>
            <p>Selamat datang di halaman Kelola Siswa.</p>

            <div class="mb-3">
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_siswa.php" class="btn btn-primary">
                    Tambah Siswa
                </a>
            </div>

            <div class="table-responsive">
                <table id="tabelSiswa"
                       class="table table-bordered table-striped table-hover">

                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th>Status Aktif</th>
                            <th>Dibuat</th>
                            <th>Diubah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
                        <tr>
                            <!-- Nomor otomatis diisi DataTables -->
                            <td></td>

                            <!-- ID asli database tetap -->
                            <td><?= htmlspecialchars($row['nis'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['nisn'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['nama'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['jenis_kelamin'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['tanggal_lahir'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['alamat'] ?? ''); ?></td>

                            <td>
                                <?php if ($row['status_aktif'] == 1) { ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php } else { ?>
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                <?php } ?>
                            </td>

                            <td><?= htmlspecialchars($row['created_at'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['updated_at'] ?? ''); ?></td>

                            <td>
                                <a href="edit_siswa.php?id=<?= urlencode($row['id']); ?>"
                                   class="btn btn-info btn-sm mb-1">
                                    Edit
                                </a>

                                <a href="hapus_siswa.php?id=<?= urlencode($row['id']); ?>"
                                   class="btn btn-danger btn-sm mb-1"
                                   onclick="return confirm('Yakin ingin menghapus data siswa ini?');">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>

                </table>
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
    const tabel = $('#tabelSiswa').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,

        // Urutan awal berdasarkan nama siswa
        order: [[4, 'asc']],

        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data siswa tidak ditemukan",
            emptyTable: "Belum ada data siswa",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            search: "Cari siswa:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },

        // Nomor mengikuti data yang tampil dan urutan saat ini
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        drawCallback: function () {
            const api = this.api();
            const info = api.page.info();

            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = info.start + i + 1;
            });
        }
    });
});
</script>

</body>
</html>
