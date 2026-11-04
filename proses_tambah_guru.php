
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Mengambil data dari form
$nip = $_POST['nip'] ?? '';
$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$konfirmasi = $_POST['konfirmasi_password'] ?? '';
$status_aktif = $_POST['status_aktif'] ?? '1';

// Validasi data
if (empty($nip)) {
    header("Location: tambah_guru.php?pesan=nip_kosong");
    exit;
}

if (!preg_match('/^[0-9]{18}$/', $nip)) {
    header("Location: tambah_guru.php?pesan=nip_tidak_valid");
    exit;
}

if (empty($nama)) {
    header("Location: tambah_guru.php?pesan=nama_kosong");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: tambah_guru.php?pesan=email_tidak_valid");
    exit;
}

if (empty($password)) {
    header("Location: tambah_guru.php?pesan=password_kosong");
    exit;
}

if ($password !== $konfirmasi) {
    header("Location: tambah_guru.php?pesan=password_tidak_cocok");
    exit;
}

// Cek NIP
$cek_nip = mysqli_prepare($koneksi,
    "SELECT nip FROM t_guru WHERE nip = ?"
);
mysqli_stmt_bind_param($cek_nip, "s", $nip);
mysqli_stmt_execute($cek_nip);

if (mysqli_stmt_get_result($cek_nip)->num_rows > 0) {
    header("Location: tambah_guru.php?pesan=nip_terdaftar");
    exit;
}

// Cek email di t_users
$cek_email = mysqli_prepare($koneksi,
    "SELECT id, role FROM t_users WHERE email = ?"
);

mysqli_stmt_bind_param($cek_email, "s", $email);
mysqli_stmt_execute($cek_email);

$result = mysqli_stmt_get_result($cek_email);
$user = mysqli_fetch_assoc($result);

// Jika email milik admin, tolak
if ($user && $user['role'] != 'guru') {
    header("Location: tambah_guru.php?pesan=email_terdaftar");
    exit;
}

try {
    mysqli_begin_transaction($koneksi);

    if ($user) {
        // Gunakan akun guru yang sudah ada
        $user_id = $user['id'];

    } else {
        // Buat akun baru
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'guru';

        $sql_user = mysqli_prepare($koneksi,
            "INSERT INTO t_users
            (name, email, password, role, created_at, updated_at)
            VALUES (?, ?, ?, ?, NOW(), NOW())"
        );

        mysqli_stmt_bind_param(
            $sql_user, "ssss", $nama, $email, $hash, $role
        );

        mysqli_stmt_execute($sql_user);

        $user_id = mysqli_insert_id($koneksi);
    }

    // Simpan data guru
    $sql_guru = mysqli_prepare($koneksi,
        "INSERT INTO t_guru
        (user_id, nip, nama, email, status_aktif)
        VALUES (?, ?, ?, ?, ?)"
    );

    $status_aktif = (int) $status_aktif;

    mysqli_stmt_bind_param(
        $sql_guru, "isssi",
        $user_id, $nip, $nama, $email, $status_aktif
    );

    mysqli_stmt_execute($sql_guru);

    mysqli_commit($koneksi);

    header("Location: kelola_guru.php?pesan=berhasil");
    exit;

} catch (Throwable $e) {
    mysqli_rollback($koneksi);

    header("Location: tambah_guru.php?pesan=gagal");
    exit;
}

// Membuat akun dan data guru
try {
    mysqli_begin_transaction($koneksi);

    // Membuat akun user
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'guru';

    $sql_user = mysqli_prepare($koneksi,
        "INSERT INTO t_users
        (name, email, password, role, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW())"
    );

    mysqli_stmt_bind_param(
        $sql_user, "ssss", $nama, $email, $hash, $role
    );

    mysqli_stmt_execute($sql_user);

    // Mengambil ID user yang baru dibuat
    $user_id = mysqli_insert_id($koneksi);

    // Memasukkan data guru
    $sql_guru = mysqli_prepare($koneksi,
        "INSERT INTO t_guru
        (user_id, nip, nama, email, status_aktif)
        VALUES (?, ?, ?, ?, ?)"
    );

    $status_aktif = (int) $status_aktif;

    mysqli_stmt_bind_param(
        $sql_guru, "isssi",
        $user_id, $nip, $nama, $email, $status_aktif
    );

    mysqli_stmt_execute($sql_guru);

    // Simpan kedua data
    mysqli_commit($koneksi);

    header("Location: kelola_guru.php?pesan=berhasil");
    exit;

} catch (Throwable $e) {

    mysqli_rollback($koneksi);

    header("Location: tambah_guru.php?pesan=gagal");
    exit;
}
?>
