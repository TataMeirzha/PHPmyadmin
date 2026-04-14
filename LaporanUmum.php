<?php
include "koneksi.php";

// Total data
$totalData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM panen"))['total'];

// Total panen (convert ke Kg)
$totalPanen = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(
        CASE 
            WHEN satuan='Ton' THEN jumlah*1000 
            ELSE jumlah 
        END
    ) as total FROM panen
"))['total'];

// Komoditas terbanyak
$komoditas = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT komoditas, COUNT(*) as jumlah 
    FROM panen 
    GROUP BY komoditas 
    ORDER BY jumlah DESC 
    LIMIT 1
"));

// Data grafik
$grafik = mysqli_query($conn, "
    SELECT komoditas, SUM(jumlah) as total 
    FROM panen 
    GROUP BY komoditas
");

$labels = [];
$data = [];

while($row = mysqli_fetch_assoc($grafik)){
    $labels[] = $row['komoditas'];
    $data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Laporan Hasil Panen</title>

<style>

    body{
    background:#f4f6f9;
    font-family: Arial, sans-serif;
    }


    /* ===== HEADER ===== */

    header{
    background: linear-gradient(135deg,#28a745,#20c997);
    color:white;
    padding:50px 20px;
    text-align:center;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    }

    header h1{
    font-weight:bold;
    margin-bottom:10px;
    }

    header p{
    opacity:0.9;
    }


    /* ===== BANNER ===== */

    img[alt="banner"]{
    width:100%;
    border-radius:12px;
    margin-bottom:30px;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
    }


    /* ===== CARD ===== */

    .card{
    border:none;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
    transition:0.3s;
    }

    .card:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,0.12);
    }

    .card h2{
    font-weight:600;
    margin-bottom:20px;
    }


    /* ===== FORM ===== */

    label{
    font-weight:600;
    margin-top:10px;
    }

    input, select{
    border-radius:6px !important;
    padding:10px !important;
    }

    input:focus, select:focus{
    border-color:#28a745 !important;
    box-shadow:0 0 5px rgba(40,167,69,0.3) !important;
    }


    /* ===== BUTTON ===== */

    button{
    border-radius:6px !important;
    font-weight:600;
    }

    button:hover{
    opacity:0.9;
    }


    /* ===== TABLE ===== */

    table{
    margin-top:10px;
    }

    table th{
    background:#28a745 !important;
    color:white;
    }

    table tr:hover{
    background:#f1f1f1;
    }


    /* ===== DELETE BUTTON ===== */

    .delete-btn{
    background:#dc3545;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:5px;
    cursor:pointer;
    }

    .delete-btn:hover{
    background:#bb2d3b;
    }

    /* === MAIN CARD (WRAPPER BESAR) === */

    .main-card{
    background:white;
    border-radius:16px;
    box-shadow:0 8px 25px rgba(0,0,0,0.1);
    padding:30px;
    max-width:1100px;
    margin:auto;
    }

    .main-card .card{
    border:none;
    }
</style>

</head>

<div class="main-card p-4">

<header class="bg-success text-white text-center p-4">

<h1>DASHBOARD PANEN TERBANYAK</h1>
<p>Analisis hasil panen petani</p>

</header>

<div class="container mt-4">

<div class="row">

<div class="col-md-4">

<div class="card text-center shadow p-3">

<h5>Total Data Panen</h5>
<h2><?= $totalData ?></h2>

</div>

</div>


<div class="col-md-4">

<div class="card text-center shadow p-3">

<h5>Total Jumlah Panen</h5>
<h2><?= $totalPanen ?> Kg</h2>

</div>

</div>


<div class="col-md-4">

<div class="card text-center shadow p-3">

<h5>Komoditas Terbanyak</h5>
<h2><?= $komoditas['komoditas'] ?></h2>

</div>

</div>

</div>


<div class="card shadow p-4 mt-4">

<h4>Grafik Hasil Panen</h4>

<canvas id="grafikPanen"></canvas>

</div>

<br>

<a href="PencatatanPanen.php" class="btn btn-success">
Kembali ke Input Data
</a>

</div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let labels = <?= json_encode($labels); ?>;
let jumlah = <?= json_encode($data); ?>;

new Chart(document.getElementById("grafikPanen"),{
    type:'bar',
    data:{
        labels:labels,
        datasets:[{
            label:"Jumlah Panen",
            data:jumlah
        }]
    }
});
</script>

</body>

</html>