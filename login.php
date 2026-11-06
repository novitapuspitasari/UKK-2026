
<?php
session_start();

if (isset($_SESSION['pesan_error'])) {
    $pesan_error = $_SESSION['pesan_error'];
    unset($_SESSION['pesan_error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Pelanggaran Siswa</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">

            <div class="col-md-6 col-lg-5 col-xl-4">

                <div class="card shadow-lg border-0">

                    <div class="card-body p-5">

                        <div class="text-center mb-5">

                            <h2 class="font-weight-bold text-primary">
                                LOGIN
                            </h2>

                            <p>
                                Silakan login untuk melanjutkan
                            </p>

                        </div>

                        <?php if (isset($pesan_error)) : ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($pesan_error); ?>
                            </div>
                        <?php endif; ?>

                        <form action="proses_login_admin.php" method="POST">

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Email</label>

                                <input type="email"
                                       name="email"
                                       class="form-control form-control-lg"
                                       placeholder="Masukkan email Anda"
                                       required>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold">Password</label>

                                <input type="password"
                                       name="password"
                                       class="form-control form-control-lg"
                                       placeholder="Masukkan password Anda"
                                       required>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary btn-lg btn-block mt-4">
                                SUBMIT
                            </button>

                        </form>

                        <div class="text-center mt-5">
                            <small class="text-muted">
                            Sistem Pelanggaran Siswa
                            </small>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

</body>
</html>
