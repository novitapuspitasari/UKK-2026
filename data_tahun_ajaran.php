```php
<?php
// data_tahun_ajaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql = "SELECT * FROM t_tahun_ajaran ORDER BY nama ASC";
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

    <title>Kelola Tahun Ajaran - Sistem Pelanggaran Siswa</title>

    <!-- Bootstrap 4.6.2 -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <!-- CSS DataTables -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1>Data Tahun Ajaran</h1>

            <p>
                
            </p>
            <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_tahun_ajaran.php" class="btn btn-primary">
                    Tambah Tahun Ajaran
                </a> 

            <!-- TABEL TAHUN AJARAN -->
            <div class="table-responsive">
                <table id="tabelTahunAjaran"
                       class="table table-bordered table-striped table-hover">

                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Status Aktif</th>
                            <th>Dibuat</th>
                            <th>Diubah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['id']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['tanggal_mulai']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['tanggal_selesai']); ?>
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
                                <a href="edit_kelola_tahun_ajaran.php?id=<?php
                                    echo urlencode($row['id']);
                                ?>" class="btn btn-info btn-sm mb-1">
                                    Edit
                                </a>

                                <a href="hapus_kelola_tahun_ajaran.php?id=<?php
                                    echo urlencode($row['id']);
                                ?>"
                                   class="btn btn-danger btn-sm mb-1"
                                   onclick="return confirm('Yakin ingin menghapus data tahun ajaran ini?');">
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

<!-- jQuery penuh -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<!-- Pengaturan DataTables -->
<script>
$(document).ready(function () {
    $('#tabelTahunAjaran').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data tahun ajaran tidak ditemukan",
            emptyTable: "Belum ada data tahun ajaran",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            search: "Cari tahun ajaran:",
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