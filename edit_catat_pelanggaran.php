
<?php
// edit_catat_pelanggaran.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// AMBIL ID CATATAN
// ==========================================

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['pesan_error'] = 'ID catatan pelanggaran tidak valid.';
    header('Location: catat_pelanggaran.php');
    exit;
}

// ==========================================
// AMBIL DATA CATATAN
// ==========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran_siswa WHERE id = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

mysqli_stmt_close($stmt);

if (!$data) {
    $_SESSION['pesan_error'] = 'Data catatan pelanggaran tidak ditemukan.';
    header('Location: catat_pelanggaran.php');
    exit;
}

// Catatan selesai tidak dapat diedit
if (strcasecmp(trim($data['status'] ?? ''), 'Selesai') === 0) {
    $_SESSION['pesan_error'] = 'Catatan berstatus Selesai tidak dapat diedit.';
    header('Location: catat_pelanggaran.php');
    exit;
}

// ==========================================
// AMBIL TAHUN AJARAN AKTIF
// ==========================================

$query_tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_tahun_ajaran
     WHERE status_aktif = 1
     ORDER BY tanggal_mulai DESC
     LIMIT 1"
);

$tahun_aktif = $query_tahun
    ? mysqli_fetch_assoc($query_tahun)
    : null;

$tahun_aktif_id = (int) ($tahun_aktif['id'] ?? 0);

// ==========================================
// PROSES SIMPAN PERUBAHAN
// ==========================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $siswa_id = filter_input(
        INPUT_POST,
        'siswa_id',
        FILTER_VALIDATE_INT
    );

    $pelanggaran_id = filter_input(
        INPUT_POST,
        'pelanggaran_id',
        FILTER_VALIDATE_INT
    );

    $tanggal = trim($_POST['tanggal'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    $status_diizinkan = [
        'Ditindaklanjuti',
        'Proses',
        'Selesai'
    ];

    $format_tanggal = DateTime::createFromFormat('!Y-m-d', $tanggal);

    // Validasi data
    if (
        !$siswa_id ||
        !$pelanggaran_id ||
        !in_array($status, $status_diizinkan, true) ||
        !$format_tanggal ||
        $format_tanggal->format('Y-m-d') !== $tanggal
    ) {
        $_SESSION['pesan_error'] = 'Data perubahan tidak valid.';
        header("Location: edit_catat_pelanggaran.php?id=" . $id);
        exit;
    }

    if ($tahun_aktif_id <= 0) {
        $_SESSION['pesan_error'] = 'Tahun ajaran aktif tidak ditemukan.';
        header("Location: edit_catat_pelanggaran.php?id=" . $id);
        exit;
    }

    // ==========================================
    // AMBIL SISWA DAN KELAS DARI PENEMPATAN
    // ==========================================

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            s.id,
            s.nama,
            ks.kelas_id,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan
         FROM t_siswa s

         INNER JOIN t_kelas_siswa ks
            ON ks.siswa_id = s.id
            AND ks.tahun_ajaran_id = ?
            AND ks.status_aktif = 1

         INNER JOIN t_kelas k
            ON ks.kelas_id = k.id

         WHERE s.id = ?
         AND s.status_aktif = 1
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $tahun_aktif_id,
        $siswa_id
    );

    mysqli_stmt_execute($stmt);

    $data_siswa = mysqli_fetch_assoc(
        mysqli_stmt_get_result($stmt)
    );

    mysqli_stmt_close($stmt);

    // ==========================================
    // AMBIL JENIS PELANGGARAN
    // ==========================================

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            nama,
            poin,
            pelanggaran_kategori_id
         FROM t_pelanggaran
         WHERE id = ?
         AND status_aktif = 1
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $pelanggaran_id
    );

    mysqli_stmt_execute($stmt);

    $data_pelanggaran = mysqli_fetch_assoc(
        mysqli_stmt_get_result($stmt)
    );

    mysqli_stmt_close($stmt);

    // ==========================================
    // PERIKSA DATA
    // ==========================================

    if (!$data_siswa) {

        $_SESSION['pesan_error'] =
            'Siswa belum memiliki penempatan kelas pada tahun ajaran aktif.';

        header("Location: edit_catat_pelanggaran.php?id=" . $id);
        exit;
    }

    if (!$data_pelanggaran) {

        $_SESSION['pesan_error'] =
            'Jenis pelanggaran tidak ditemukan atau tidak aktif.';

        header("Location: edit_catat_pelanggaran.php?id=" . $id);
        exit;
    }

    // ==========================================
    // DATA SISWA
    // ==========================================

    $nama_siswa = $data_siswa['nama'];

    $kelas_id = (int) $data_siswa['kelas_id'];

    $nama_kelas = $data_siswa['nama_kelas'];

    // ==========================================
    // DATA PELANGGARAN
    // ==========================================

    $nama_pelanggaran = $data_pelanggaran['nama'];

    $kategori_id = (int) $data_pelanggaran['pelanggaran_kategori_id'];

    $poin = (int) $data_pelanggaran['poin'];

    // Guru pelapor tetap mengikuti catatan awal
    $guru_id = (int) $data['guru_id'];

    // Tindakan tidak digunakan pada form
    $tindakan = '';

    // ==========================================
    // UPDATE CATATAN PELANGGARAN
    // ==========================================

    $stmt = mysqli_prepare(
        $koneksi,
        "UPDATE t_pelanggaran_siswa SET
            siswa_id = ?,
            nama_siswa = ?,
            kelas_id = ?,
            nama_kelas = ?,
            pelanggaran_id = ?,
            nama_pelanggaran = ?,
            pelanggaran_kategori_id = ?,
            poin = ?,
            tanggal = ?,
            status = ?,
            keterangan = ?,
            tindakan = ?
         WHERE id = ?
         AND status <> 'Selesai'"
    );

    if (!$stmt) {

        $_SESSION['pesan_error'] =
            'Query update gagal: ' . mysqli_error($koneksi);

        header("Location: edit_catat_pelanggaran.php?id=" . $id);
        exit;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "isisisiissssi",
        $siswa_id,
        $nama_siswa,
        $kelas_id,
        $nama_kelas,
        $pelanggaran_id,
        $nama_pelanggaran,
        $kategori_id,
        $poin,
        $tanggal,
        $status,
        $keterangan,
        $tindakan,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        $_SESSION['pesan_sukses'] =
            'Catatan pelanggaran berhasil diperbarui.';

        header('Location: catat_pelanggaran.php');
        exit;

    } else {

        $_SESSION['pesan_error'] =
            'Gagal memperbarui data: ' . mysqli_stmt_error($stmt);

        mysqli_stmt_close($stmt);
    }
}

// ==========================================
// DAFTAR SISWA BESERTA PENEMPATAN KELAS
// ==========================================

$daftar_siswa = [];
$kelas_siswa = [];

if ($tahun_aktif_id > 0) {

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT
            s.id,
            s.nis,
            s.nama,
            ks.kelas_id,
            k.nama AS nama_kelas,
            k.tingkat,
            k.jurusan

         FROM t_siswa s

         LEFT JOIN t_kelas_siswa ks
            ON ks.siswa_id = s.id
            AND ks.tahun_ajaran_id = ?
            AND ks.status_aktif = 1

         LEFT JOIN t_kelas k
            ON ks.kelas_id = k.id

         WHERE (
            s.status_aktif = 1
            OR s.id = ?
         )

         ORDER BY s.nama ASC"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $tahun_aktif_id,
        $data['siswa_id']
    );

    mysqli_stmt_execute($stmt);

    $hasil_siswa = mysqli_stmt_get_result($stmt);

    while ($s = mysqli_fetch_assoc($hasil_siswa)) {

        $kelas_tampil = '';

        if (!empty($s['nama_kelas'])) {

            $kelas_tampil =
                $s['nama_kelas'] . ' - ' .
                $s['tingkat'] . ' - ' .
                $s['jurusan'];
        }

        $daftar_siswa[] = $s;

        $kelas_siswa[(string) $s['id']] = [
            'kelas_id' => (int) ($s['kelas_id'] ?? 0),
            'nama_kelas' => $kelas_tampil
        ];
    }

    mysqli_stmt_close($stmt);
}

// ==========================================
// DAFTAR JENIS PELANGGARAN
// ==========================================

$pelanggaran = mysqli_query(
    $koneksi,
    "SELECT
        id,
        nama,
        poin,
        pelanggaran_kategori_id
     FROM t_pelanggaran
     WHERE status_aktif = 1
        OR id = " . (int) $data['pelanggaran_id'] . "
     ORDER BY nama ASC"
);

// ==========================================
// AMBIL TAHUN AJARAN CATATAN
// ==========================================

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT nama
     FROM t_tahun_ajaran
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $data['tahun_ajaran_id']
);

mysqli_stmt_execute($stmt);

$tahun_data = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt)
);

mysqli_stmt_close($stmt);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Edit Catatan Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css"
        rel="stylesheet"
    >

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <?php include 'layout/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4">

            <h2 class="mb-4">Edit Catatan Pelanggaran</h2>

            <!-- PESAN ERROR -->
            <?php if (isset($_SESSION['pesan_error'])) { ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($_SESSION['pesan_error']); ?>

                </div>

                <?php unset($_SESSION['pesan_error']); ?>

            <?php } ?>

            <!-- FORM EDIT -->
            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">
                    Form Edit Pelanggaran
                </div>

                <div class="card-body">

                    <form method="POST">

                        <div class="row">

                            <!-- SISWA -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">Siswa</label>

                                <select
                                    name="siswa_id"
                                    id="siswa"
                                    class="form-select"
                                    required
                                >

                                    <?php foreach ($daftar_siswa as $s) { ?>

                                        <option
                                            value="<?= (int) $s['id']; ?>"
                                            <?= (int) $data['siswa_id'] === (int) $s['id'] ? 'selected' : ''; ?>
                                        >

                                            <?= htmlspecialchars(
                                                $s['nis'] . ' - ' . $s['nama']
                                            ); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- TAHUN AJARAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">Tahun Ajaran</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tahun_data['nama'] ?? '-'); ?>"
                                    readonly
                                >

                                <small class="text-muted">
                                    Tahun ajaran catatan ini tidak diubah saat mengedit.
                                </small>

                            </div>

                            <!-- KELAS OTOMATIS -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">Kelas</label>

                                <input
                                    type="text"
                                    id="kelas"
                                    class="form-control"
                                    value=""
                                    placeholder="Kelas otomatis terisi"
                                    readonly
                                >

                                <input
                                    type="hidden"
                                    name="kelas_id"
                                    id="kelas_id"
                                    value=""
                                >

                                <small class="text-muted">
                                    Kelas otomatis mengikuti penempatan siswa pada tahun ajaran aktif.
                                </small>

                            </div>

                            <!-- JENIS PELANGGARAN -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Jenis Pelanggaran
                                </label>

                                <select
                                    name="pelanggaran_id"
                                    id="pelanggaran"
                                    class="form-select"
                                    required
                                >

                                    <?php while ($p = mysqli_fetch_assoc($pelanggaran)) { ?>

                                        <option
                                            value="<?= (int) $p['id']; ?>"
                                            <?= (int) $data['pelanggaran_id'] === (int) $p['id'] ? 'selected' : ''; ?>
                                        >

                                            <?= htmlspecialchars($p['nama']); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                                <small class="text-muted">
                                    Ketik nama pelanggaran untuk mencari.
                                </small>

                            </div>

                            <!-- TANGGAL -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">Tanggal</label>

                                <input
                                    type="date"
                                    name="tanggal"
                                    class="form-control"
                                    value="<?= htmlspecialchars($data['tanggal']); ?>"
                                    required
                                >

                            </div>

                            <!-- STATUS -->
                            <div class="col-md-6 mb-3">

                                <label class="form-label">Status</label>

                                <select
                                    name="status"
                                    class="form-select"
                                    required
                                >

                                    <?php
                                    $daftar_status = [
                                        'Ditindaklanjuti',
                                        'Proses',
                                        'Selesai'
                                    ];

                                    foreach ($daftar_status as $st) {
                                    ?>

                                        <option
                                            value="<?= htmlspecialchars($st); ?>"
                                            <?= $data['status'] === $st ? 'selected' : ''; ?>
                                        >

                                            <?= htmlspecialchars($st); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <!-- KETERANGAN -->
                            <div class="col-md-12 mb-3">

                                <label class="form-label">Keterangan</label>

                                <textarea
                                    name="keterangan"
                                    class="form-control"
                                    rows="3"
                                ><?= htmlspecialchars($data['keterangan'] ?? ''); ?></textarea>

                            </div>

                        </div>

                        <!-- TOMBOL -->
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Simpan Perubahan
                        </button>

                        <a
                            href="catat_pelanggaran.php"
                            class="btn btn-danger"
                        >
                            Kembali
                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

<script>

// ==========================================
// DATA KELAS SISWA DARI DATABASE
// ==========================================

const kelasSiswa = <?= json_encode(
    $kelas_siswa,
    JSON_HEX_TAG |
    JSON_HEX_APOS |
    JSON_HEX_QUOT |
    JSON_HEX_AMP
); ?>;

// ==========================================
// ELEMEN KELAS
// ==========================================

const inputKelas = document.getElementById("kelas");

const inputKelasId = document.getElementById("kelas_id");

// ==========================================
// FUNGSI ISI KELAS OTOMATIS
// ==========================================

function isiKelas(siswaId) {

    const data = kelasSiswa[String(siswaId)];

    if (data && data.kelas_id > 0) {

        inputKelas.value = data.nama_kelas;

        inputKelasId.value = data.kelas_id;

    } else {

        inputKelas.value = "";

        inputKelasId.value = "";

    }

}

// ==========================================
// PENCARIAN SISWA
// ==========================================

const siswaSelect = new TomSelect("#siswa", {

    create: false,

    placeholder: "Cari nama atau NIS siswa...",

    onChange: function(value) {

        isiKelas(value);

    }

});

// ==========================================
// PENCARIAN JENIS PELANGGARAN
// ==========================================

new TomSelect("#pelanggaran", {

    create: false,

    placeholder: "Cari jenis pelanggaran..."

});

// ==========================================
// ISI KELAS SAAT HALAMAN DIBUKA
// ==========================================

isiKelas(siswaSelect.getValue());

</script>

</body>
</html>
