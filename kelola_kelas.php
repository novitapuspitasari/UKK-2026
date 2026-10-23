
<?php
// kelola_kelas.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_kelas ORDER BY nama ASC";
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

    <title>Kelola Kelas - Sistem Pelanggaran Siswa</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h1>Kelola Kelas</h1>

            <p>Selamat datang di halaman Kelola Kelas</p>

            <a href="dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>

            <a href="tambah_kelas.php" class="btn btn-primary">
                Tambah Kelas
            </a>

            <div class="table-responsive mt-3">
                <table id="tabelKelas"
                       class="table table-bordered table-striped table-hover">

                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Tingkat</th>
                            <th>Jurusan</th>
                            <th>Status Aktif</th>
                            <th>Dibuat</th>
                            <th>Diubah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
                        <tr>
                            <td></td>
                            <td>
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['tingkat']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['jurusan']); ?>
                            </td>

                            <td>
                                <?php
                                echo ($row['status_aktif'] == 1)
                                    ? 'Aktif'
                                    : 'Tidak Aktif';
                                ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['created_at'] ?? ''); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['updated_at'] ?? ''); ?>
                            </td>

                            <td>
                                <a href="edit_kelas.php?id=<?php
                                    echo urlencode($row['id']);
                                ?>" class="btn btn-info btn-sm mb-1">
                                    Edit
                                </a>

                                <a href="hapus_kelas.php?id=<?php
                                    echo urlencode($row['id']);
                                ?>"
                                   class="btn btn-danger btn-sm mb-1"
                                   onclick="return confirm('Yakin ingin menghapus data kelas ini?');">
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelKelas').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,

        // Nomor urut otomatis
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ],

        // Urutkan berdasarkan Nama Kelas
        order: [[2, 'asc']],

        drawCallback: function () {
            const api = this.api();
            const info = api.page.info();

            api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                cell.innerHTML = info.start + i + 1;
            });
        },

        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data kelas tidak ditemukan",
            emptyTable: "Belum ada data kelas",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            search: "Cari kelas:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>

</body>
</html>