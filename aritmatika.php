
<?php
// aritmatika.php

$nilai1 = 10;
$nilai2 = 5;

// Menghitung pertambahan
$pertambahan = $nilai1 + $nilai2;

// Menghitung pengurangan
$pengurangan = $nilai1 - $nilai2;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perhitungan Aritmatika</title>
</head>
<body>

    <h2>Perhitungan Aritmatika</h2>

    <p>Nilai 1 = <?php echo $nilai1; ?></p>
    <p>Nilai 2 = <?php echo $nilai2; ?></p>

    <h3>Hasil Perhitungan</h3>

    <p>Pertambahan:
        <?php echo $nilai1 . " + " . $nilai2 . " = " . $pertambahan; ?>
    </p>

    <p>Pengurangan:
        <?php echo $nilai1 . " - " . $nilai2 . " = " . $pengurangan; ?>
    </p>

</body>
</html>
