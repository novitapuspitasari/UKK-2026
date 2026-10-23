
<?php
// catat_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Daftar siswa
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama
     FROM t_siswa
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Daftar tahun ajaran
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY id DESC"
);

// Daftar kelas
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama, tingkat, jurusan
     FROM t_kelas
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Daftar pelanggaran
$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT id, nama, poin, pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Daftar guru
$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_guru
     WHERE status_aktif = 1
     ORDER BY nama ASC"
);

// Data catatan pelanggaran
$sql = "SELECT
            ps.*,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas,
            ta.nama AS tahun_ajaran,
            p.nama AS nama_pelanggaran,
            g.nama AS nama_guru
        FROM t_pelanggaran_siswa ps
        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id
        LEFT JOIN t_kelas k
            ON ps.kelas_id = k.id
        LEFT JOIN t_tahun_ajaran ta
            ON ps.tahun_ajaran_id = ta.id
        LEFT JOIN t_pelanggaran p
            ON ps.pelanggaran_id = p.id
        LEFT JOIN t_guru g
            ON ps.guru_id = g.id
        ORDER BY ps.id DESC";

$hasil = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- DataTables Bootstrap 5 -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css"
          rel="stylesheet">
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Catat Pelanggaran Siswa</h2>

            <?php if (isset($_SESSION['pesan_sukses'])) { ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['pesan_sukses']); ?>
                </div>
                <?php unset($_SESSION['pesan_sukses']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['pesan_error'])) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>
                </div>
                <?php unset($_SESSION['pesan_error']); ?>
            <?php } ?>

            <!-- FORM CATAT PELANGGARAN -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header fw-bold">
                    Form Catat Pelanggaran
                </div>

                <div class="card-body">
                    <form action="proses_simpan_pelanggaran.php" method="POST">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Siswa</label>
                                <select name="siswa_id" class="form-select" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                        <option value="<?= (int) $s['id']; ?>">
                                            <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Ajaran</label>
                                <select name="tahun_ajaran_id" class="form-select" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>
                                    <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                        <option value="<?= (int) $ta['id']; ?>">
                                            <?= htmlspecialchars($ta['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                        <option value="<?= (int) $k['id']; ?>">
                                            <?= htmlspecialchars(
                                                $k['nama'] . ' - ' .
                                                $k['tingkat'] . ' - ' .
                                                $k['jurusan']
                                            ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pelanggaran</label>
                                <select name="pelanggaran_id"
                                        id="pelanggaran"
                                        class="form-select"
                                        required>
                                    <option value="">-- Pilih Pelanggaran --</option>
                                    <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>
                                        <option
                                            value="<?= (int) $p['id']; ?>"
                                            data-poin="<?= (int) $p['poin']; ?>"
                                            data-kategori="<?= (int) $p['pelanggaran_kategori_id']; ?>">
                                            <?= htmlspecialchars($p['nama'] . ' (' . $p['poin'] . ' poin)'); ?>
                                        </option>
                                    <?php } ?>
                                </select>

                                <input type="hidden"
                                       name="pelanggaran_kategori_id"
                                       id="kategori">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guru Pelapor</label>
                                <select name="guru_id" class="form-select" required>
                                    <option value="">-- Pilih Guru --</option>
                                    <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                        <option value="<?= (int) $g['id']; ?>">
                                            <?= htmlspecialchars($g['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?= date('Y-m-d'); ?>"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Poin Pelanggaran</label>
                                <input type="number"
                                       name="poin"
                                       id="poin"
                                       class="form-control"
                                       min="0"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                                    <option value="Proses">Proses</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tindakan</label>
                                <textarea name="tindakan"
                                          class="form-control"
                                          rows="3"></textarea>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            Simpan Pelanggaran
                        </button>

                        <a href="dashboard.php" class="btn btn-secondary">
                            Kembali
                        </a>
                    </form>
                </div>
            </div>

            <!-- TABEL CATATAN PELANGGARAN -->
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    Data Catatan Pelanggaran
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelPelanggaran"
                               class="table table-bordered table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Pelanggaran</th>
                                    <th>Guru</th>
                                    <th>Tanggal</th>
                                    <th>Poin</th>
                                    <th>Tindakan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($hasil && mysqli_num_rows($hasil) > 0) { ?>
                                    <?php $no = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($hasil)) { ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($row['nama_siswa'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['nama_pelanggaran'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['nama_guru'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['tanggal'] ?? '-'); ?></td>
                                            <td><?= (int) ($row['poin'] ?? 0); ?></td>
                                            <td><?= htmlspecialchars($row['tindakan'] ?? '-'); ?></td>
                                            <td>
                                                <?php
                                                $status = $row['status'] ?? '';
                                                $warna = 'secondary';

                                                if ($status === 'Selesai') {
                                                    $warna = 'success';
                                                } elseif ($status === 'Proses') {
                                                    $warna = 'warning';
                                                } elseif ($status === 'Ditindaklanjuti') {
                                                    $warna = 'primary';
                                                }
                                                ?>
                                                <span class="badge bg-<?= $warna; ?>">
                                                    <?= htmlspecialchars($status ?: '-'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php } ?>
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

<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#tabelPelanggaran').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada catatan pelanggaran",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        },
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'asc']]
    });
});

// Otomatis mengisi poin dan kategori
const pilihanPelanggaran = document.getElementById('pelanggaran');
const inputPoin = document.getElementById('poin');
const inputKategori = document.getElementById('kategori');

pilihanPelanggaran.addEventListener('change', function () {
    const opsi = this.options[this.selectedIndex];

    inputPoin.value = opsi.dataset.poin || '';
    inputKategori.value = opsi.dataset.kategori || '';
});
</script>

</body>
</html>
