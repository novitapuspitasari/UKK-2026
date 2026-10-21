
<?php
// penempatan_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Ambil daftar siswa aktif
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil daftar tahun ajaran aktif
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Ambil daftar kelas aktif
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Ambil data penempatan siswa
$sql = "SELECT
            ks.id,
            s.nis,
            s.nama AS nama_siswa,
            ta.nama AS tahun_ajaran,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan,
            ks.tanggal_mulai,
            ks.tanggal_selesai,
            ks.status_aktif
        FROM t_kelas_siswa ks
        LEFT JOIN t_siswa s ON ks.siswa_id = s.id
        LEFT JOIN t_tahun_ajaran ta
            ON ks.tahun_ajaran_id = ta.id
        LEFT JOIN t_kelas k ON ks.kelas_id = k.id
        ORDER BY ks.id DESC";

$hasil = mysqli_query($koneksi, $sql);

if (!$siswa || !$tahun || !$kelas || !$hasil) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Penempatan Siswa - Sistem Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- CSS DataTables Bootstrap 5 -->
    <link
        href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"
        rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <h2>Penempatan Siswa</h2>

            <p>
                Selamat datang di halaman Penempatan Siswa.
            </p>

            <p>
                <a href="dashboard.php" class="btn btn-secondary">
                    Kembali ke Dashboard
                </a>
            </p>

            <!-- PESAN SUKSES -->
            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_sukses']);
                    unset($_SESSION['pesan_sukses']);
                    ?>
                </div>
            <?php } ?>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?php
                    echo htmlspecialchars($_SESSION['pesan_error']);
                    unset($_SESSION['pesan_error']);
                    ?>
                </div>
            <?php } ?>

            <!-- FORM PENEMPATAN SISWA -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Form Penempatan Siswa</h5>
                </div>

                <div class="card-body">
                    <form
                        action="proses_simpan_penempatan.php"
                        method="POST">

                        <div class="form-group mb-3">
                            <label for="siswa_id">Siswa</label>
                            <select
                                name="siswa_id"
                                id="siswa_id"
                                class="form-control"
                                required>

                                <option value="">-- Pilih Siswa --</option>

                                <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                    <option value="<?php echo $s['id']; ?>">
                                        <?php
                                        echo htmlspecialchars(
                                            $s['nis'] . ' - ' . $s['nama']
                                        );
                                        ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tahun_ajaran_id">
                                Tahun Ajaran
                            </label>

                            <select
                                name="tahun_ajaran_id"
                                id="tahun_ajaran_id"
                                class="form-control"
                                required>

                                <option value="">
                                    -- Pilih Tahun Ajaran --
                                </option>

                                <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                    <option value="<?php echo $ta['id']; ?>">
                                        <?php echo htmlspecialchars($ta['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="kelas_id">Kelas</label>

                            <select
                                name="kelas_id"
                                id="kelas_id"
                                class="form-control"
                                required>

                                <option value="">-- Pilih Kelas --</option>

                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?php echo $k['id']; ?>">
                                        <?php
                                        echo htmlspecialchars(
                                            $k['nama'] .
                                            ' - Tingkat ' . $k['tingkat'] .
                                            ' - ' . $k['jurusan']
                                        );
                                        ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tanggal_mulai">Tanggal Mulai</label>
                            <input
                                type="date"
                                name="tanggal_mulai"
                                id="tanggal_mulai"
                                class="form-control"
                                required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tanggal_selesai">
                                Tanggal Selesai
                            </label>
                            <input
                                type="date"
                                name="tanggal_selesai"
                                id="tanggal_selesai"
                                class="form-control"
                                required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status_aktif">Status</label>

                            <select
                                name="status_aktif"
                                id="status_aktif"
                                class="form-control"
                                required>

                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Penempatan
                        </button>

                    </form>
                </div>
            </div>

            <!-- TABEL DATA PENEMPATAN -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Data Penempatan Siswa</h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">

                        <table
                            id="tabelPenempatan"
                            class="table table-bordered table-striped table-hover">

                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $no = 1;

                                while ($row = mysqli_fetch_assoc($hasil)) {
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['nis'] ?? '-'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['nama_siswa'] ?? '-'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['tahun_ajaran'] ?? '-'
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
                                            echo htmlspecialchars(
                                                $row['tingkat'] ?? '-'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['jurusan'] ?? '-'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['tanggal_mulai'] ?? '-'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo htmlspecialchars(
                                                $row['tanggal_selesai'] ?? '-'
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo ($row['status_aktif'] == 1)
                                                ? 'Aktif'
                                                : 'Tidak Aktif';
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

<!-- jQuery penuh -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<!-- Pengaturan DataTables -->
<script>
$(document).ready(function () {
    $('#tabelPenempatan').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: true,
        searching: true,
        language: {
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Data penempatan siswa tidak ditemukan",
            emptyTable: "Belum ada data penempatan siswa",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            search: "Cari penempatan:",
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