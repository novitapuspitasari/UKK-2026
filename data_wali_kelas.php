
<?php
// data_wali_kelas.php
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

    <title>Data Wali Kelas - Sistem Pelanggaran Siswa</title>

    <!-- Bootstrap 4 -->
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

            <h1>Data Wali Kelas</h1>

            <p>Selamat datang di halaman Penempatan Siswa
            </p>
            <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>

                <a href="tambah_wali_kelas.php" class="btn btn-primary">
                    Tambah Wali Kelas
                </a>

            <!-- TABEL WALI KELAS -->
            <div class="table-responsive">
                <table id="tabelWaliKelas"
                       class="table table-bordered table-striped table-hover">

                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
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
                            <td>
                                <?php echo htmlspecialchars($row['id']); ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['nama_guru'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['nama_kelas'] ?? '-'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo (isset($row['status_aktif'])
                                    && $row['status_aktif'] == 1)
                                    ? 'Aktif'
                                    : 'Tidak Aktif';
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['created_at'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row['updated_at'] ?? ''
                                );
                                ?>
                            </td>

                            <td>
                                <a
                                    href="edit_wali_kelas.php?id=<?php
                                        echo urlencode($row['id']);
                                    ?>"
                                    class="btn btn-info btn-sm mb-1">
                                    Edit
                                </a>

                                <a
                                    href="hapus_wali_kelas.php?id=<?php
                                        echo urlencode($row['id']);
                                    ?>"
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
    $('#tabelWaliKelas').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
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
        }
    });
});
</script>

</body>
</html>
