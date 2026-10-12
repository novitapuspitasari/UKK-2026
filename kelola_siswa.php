<?php
// kelola_siswa.php
include 'includes/cek_session.php';
include 'config/koneksi.php';

$sql   ="SELECT * FROM t_siswa ORDER BY nama ASC";
$hasil = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html>
    <head>
       <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <title>Kelola Siswa - Sistem Pelanggaran Siswa</title>
    </head>
    <body>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
        <h1>Kelola Siswa</h1>
        <p><a href="dashboard.php">Kembali ke Dashboard</a> | <a href="tambah_siswa.php">Tambah Siswa</a></p>


        <table class="table table-bordered">
            <tr>
        
                <th>NIS</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Status Aktif</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($hasil)){ ?>
            <tr>
                <td><?php echo $row['nis']; ?></td>
                <td><?php echo $row['nisn']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['jenis_kelamin']; ?></td>
                <td><?php echo $row['tanggal_lahir']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php if ($row['status_aktif'] == 1) {
                     echo "Aktif";
                } else {
                    echo "Tidak Aktif";
                }
                ?>
            </td>
               
                <td>
                    <a href="edit_guru.php?id=<?php echo $row['id']; ?>"></a>
                         <button type="button" class="btn btn-info">Edit</button>
                    <a href="hapus_guru.php?id=<?php echo $row['id']; ?>"
                    onclick="return confirm('Yakin hapus barang ini?');"></a>
                        <button type="button" class="btn btn-secondary">Hapus</button>
                </td> 
            </tr>
        <?php } ?>
        </table>
    </body>
</html>