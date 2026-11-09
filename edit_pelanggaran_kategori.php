
<?php
// edit_pelanggaran_kategori.php

include 'includes/cek_session.php';
include 'config/koneksi.php';

$id = $_GET['id'] ?? '';

$sql = mysqli_prepare($koneksi,
    "SELECT * FROM t_pelanggaran_kategori WHERE id = ?"
);

mysqli_stmt_bind_param($sql, "i", $id);
mysqli_stmt_execute($sql);

$hasil = mysqli_stmt_get_result($sql);
$data = mysqli_fetch_assoc($hasil);

if (!$data) {
    die("Data kategori pelanggaran tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Pelanggaran Kategori</title>

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

            <h1 class="mb-4">Edit Pelanggaran Kategori</h1>

            <div class="card shadow-sm">

                <div class="card-header bg-primary text-white">
                    Form Edit Pelanggaran Kategori
                </div>

                <div class="card-body">

                    <form action="proses_edit_pelanggaran_kategori.php"
                          method="POST">

                        <input type="hidden" name="id"
                               value="<?= htmlspecialchars($data['id']); ?>">

                        <!-- Nama Kategori -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Kategori
                            </label>

                            <input type="text"
                                   name="nama"
                                   id="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama']); ?>"
                                   required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                Deskripsi
                            </label>

                            <textarea name="deskripsi"
                                      id="deskripsi"
                                      class="form-control"
                                      rows="3"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
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

                                <option value="1"
                                    <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-primary btn-sm">
                                Update Pelanggaran Kategori
                            </button>

                            <a href="pelanggaran_kategori.php"
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
