<?php
session_start();
include "koneksi.php";

// Cek login (opsional, kalau masih pakai login)
if(!isset($_SESSION['login'])){
    header("Location: login.php");
    exit;
}

// SIMPAN DATA
if(isset($_POST['simpan'])){
    $tanggal   = $_POST['tanggal_panen'];
    $komoditas = $_POST['komoditas_panen'];
    $jumlah    = $_POST['jumlah_panen'];
    $satuan    = $_POST['satuan_panen'];
    $lokasi    = $_POST['lokasi_panen'];

    $query = "INSERT INTO panen (tanggal, komoditas, jumlah, satuan, lokasi) 
              VALUES ('$tanggal','$komoditas','$jumlah','$satuan','$lokasi')";

    if(mysqli_query($conn, $query)){
        header("Location: PencatatanPanen.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// HAPUS DATA
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM panen WHERE id='$id'");
    header("Location: PencatatanPanen.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pencatatan Hasil Panen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{ font-family: Arial; background:#f4f6f9; }
        header{ background:linear-gradient(135deg,#28a745,#20c997); color:white; padding:40px; text-align:center; }
        .main-card{ background:white; border-radius:16px; padding:30px; max-width:1100px; margin:20px auto; box-shadow:0 8px 25px rgba(0,0,0,0.1); }
        .card{ padding:20px; margin-bottom:20px; border-radius:12px; }
    </style>
</head>

<body>

<div class="main-card">

<header>
    <h1>SISTEM PENCATATAN HASIL PANEN</h1>
    <p>Kelola data panen dengan mudah</p>
</header>

<img src="BANNER.PNG" class="img-fluid w-100 my-3" style="border-radius:10px;">

<div class="container">

<!-- FORM INPUT -->
<div class="card shadow-sm">
    <h2>Input Data Panen</h2>

    <form method="POST">

        <label>Tanggal Panen</label>
        <input type="date" name="tanggal_panen" class="form-control" required>

        <label class="mt-2">Komoditas</label>
        <select name="komoditas_panen" class="form-control">
            <option>Padi</option>
            <option>Jagung</option>
            <option>Cabai</option>
            <option>Kedelai</option>
        </select>

        <label class="mt-2">Jumlah</label>
        <input type="number" name="jumlah_panen" class="form-control" required>

        <label class="mt-2">Satuan</label>
        <select name="satuan_panen" class="form-control">
            <option>Kg</option>
            <option>Ton</option>
        </select>

        <label class="mt-2">Lokasi</label>
        <select name="lokasi_panen" class="form-control">
            <option>Lahan A</option>
            <option>Lahan B</option>
            <option>Lahan C</option>
        </select>

        <button type="submit" name="simpan" class="btn btn-success mt-3 w-100">
            Simpan Data
        </button>

    </form>
</div>

<!-- TABEL DATA -->
<div class="card shadow-sm">
    <h2>Riwayat Panen</h2>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-success">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Komoditas</th>
                <th>Jumlah</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $no = 1;
        $tampil = mysqli_query($conn, "SELECT * FROM panen ORDER BY id DESC");

        while($data = mysqli_fetch_assoc($tampil)){
        ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $data['tanggal']; ?></td>
                <td><?= $data['komoditas']; ?></td>
                <td><?= $data['jumlah'] . " " . $data['satuan']; ?></td>
                <td><?= $data['lokasi']; ?></td>
                <td>
                    <a href="?hapus=<?= $data['id']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Yakin hapus data?')">
                       Hapus
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>

    </table>

            <div class="mt-3 d-flex gap-2">
                <a href="LaporanUmum.php" class="btn btn-success">Dashboard Panen Terbanyak</a>
                <a href="LaporanPerKomoditas.php" class="btn btn-primary">Laporan Panen Per Komoditas</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>