<?php
// tambah_guru.php
include 'includes/cek_session.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Guru</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <?php include 'layout/sidebar.php'; ?>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-4">

            <h1 class="mb-4">Tambah Guru</h1>

            <!-- NOTIFIKASI -->
            <?php if (isset($_GET['pesan'])): ?>
                <?php
                $pesan = $_GET['pesan'];

                $daftar_pesan = [
                    'nip_kosong' => 'NIP wajib diisi.',
                    'nip_tidak_valid' => 'NIP harus tepat 18 digit angka.',
                    'nama_kosong' => 'Nama guru wajib diisi.',
                    'email_tidak_valid' => 'Format email tidak valid.',
                    'password_tidak_cocok' => 'Konfirmasi password tidak cocok.',
                    'password_kosong' => 'Password wajib diisi.',
                    'email_terdaftar' => 'Email sudah terdaftar.',
                    'nip_terdaftar' => 'NIP sudah terdaftar.',
                    'gagal' => 'Data guru gagal ditambahkan. Periksa kembali data dan struktur tabel.',
                    'berhasil' => 'Data guru berhasil ditambahkan.'
                ];

                $jenis_alert = ($pesan === 'berhasil') ? 'success' : 'danger';

                if (isset($daftar_pesan[$pesan])) {
                    $isi_pesan = $daftar_pesan[$pesan];
                } else {
                    $isi_pesan = 'Terjadi kesalahan. Silakan periksa kembali data.';
                }
                ?>

                <div class="alert alert-<?= $jenis_alert ?> alert-dismissible fade show"
                     role="alert">
                    <?= htmlspecialchars($isi_pesan) ?>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Tutup"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Form Tambah Guru
                </div>

                <div class="card-body">

                    <form action="proses_tambah_guru.php"
                          method="POST"
                          id="formTambahGuru">

                        <!-- NIP -->
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text"
                                   name="nip"
                                   id="nip"
                                   class="form-control"
                                   maxlength="18"
                                   minlength="18"
                                   pattern="[0-9]{18}"
                                   inputmode="numeric"
                                   value="<?= isset($_GET['nip']) ? htmlspecialchars($_GET['nip']) : '' ?>"
                                   required>
                            <div class="form-text">
                                NIP harus terdiri dari tepat 18 digit angka.
                            </div>
                        </div>

                        <!-- Nama Guru -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Guru</label>
                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   value="<?= isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : '' ?>"
                                   required>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>"
                                   required>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-3">
                            <label for="konfirmasi_password" class="form-label">
                                Konfirmasi Password
                            </label>
                            <input type="password"
                                   name="konfirmasi_password"
                                   id="konfirmasi_password"
                                   class="form-control"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <!-- Status Aktif -->
                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status Aktif
                            </label>
                            <select name="status_aktif"
                                    id="status_aktif"
                                    class="form-select"
                                    required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary btn-sm">
                                Simpan Guru
                            </button>

                            <a href="kelola_guru.php"
                               class="btn btn-danger btn-sm">
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>