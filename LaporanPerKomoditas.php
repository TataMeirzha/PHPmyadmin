<?php
include "koneksi.php";

// ambil semua data
$data = mysqli_query($conn, "SELECT * FROM panen");

$rows = [];
while($d = mysqli_fetch_assoc($data)){
    $rows[] = $d;
}

// ✅ DATA TAMBAHAN UNTUK CHART (PINDAH KE LUAR WHILE)
$grafik = mysqli_query($conn, "
    SELECT komoditas, SUM(
        CASE 
            WHEN satuan='Ton' THEN jumlah*1000 
            ELSE jumlah 
        END
    ) as total 
    FROM panen 
    GROUP BY komoditas
");

$labels = [];
$dataChart = [];

while($row = mysqli_fetch_assoc($grafik)){
    $labels[] = $row['komoditas'];
    $dataChart[] = $row['total'];
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
    font-family:Arial, sans-serif;
    }

    header{
    background:linear-gradient(135deg,#28a745,#20c997);
    }

    .card{
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    }

    h2{
    font-weight:bold;
    }

    select{
    margin-top:10px;
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

<body>

<div class="main-card p-4">

<header class="bg-success text-white text-center p-4">
<h1>LAPORAN PANEN PER KOMODITAS</h1>
<p>Dashboard Data Panen</p>
</header>

<div class="container mt-4">

<a href="PencatatanPanen.php" class="btn btn-secondary mb-3">⬅ Kembali</a>

<!-- FILTER -->
<div class="card p-3 shadow mb-4">
<label>Filter Komoditas</label>
<select id="filterKomoditas" class="form-control">
<option value="semua">Semua</option>
<option>Padi</option>
<option>Jagung</option>
<option>Cabai</option>
<option>Kedelai</option>
</select>
</div>

<!-- INFO -->
<div class="row">

<div class="col-md-4">
<div class="card text-center shadow p-3">
<h5>Total Data</h5>
<h2 id="totalData">0</h2>
</div>
</div>

<div class="col-md-4">
<div class="card text-center shadow p-3">
<h5>Total Panen</h5>
<h2 id="totalPanen">0</h2>
</div>
</div>

<div class="col-md-4">
<div class="card text-center shadow p-3">
<h5>Komoditas Terbanyak</h5>
<h2 id="komoditasTerbanyak">-</h2>
</div>
</div>

</div>

<!-- GRAFIK 1 -->
<div class="card shadow p-4 mt-4">
<h4>Grafik Total per Komoditas</h4>
<canvas id="grafikKomoditas"></canvas>
</div>

<!-- GRAFIK 2 -->
<div class="card shadow p-4 mt-4">
<h4>Grafik Data Panen</h4>
<canvas id="grafikPanen"></canvas>
</div>

<!-- GRAFIK 3 (DARI DATABASE LANGSUNG) -->
<div class="card shadow p-4 mt-4">
<h4>Grafik Total Panen (Database)</h4>
<canvas id="grafikDatabase"></canvas>
</div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let dataPanen = <?= json_encode($rows); ?>;

let chart1, chart2;

function konversiKeKg(item){
let jumlah = Number(item.jumlah);
if(item.satuan === "Ton"){
jumlah = jumlah * 1000;
}
return jumlah;
}

function renderData(filter="semua"){

let filtered = dataPanen;

if(filter !== "semua"){
filtered = dataPanen.filter(d => d.komoditas === filter);
}

// TOTAL
let totalData = filtered.length;
let totalPanen = 0;
let komoditasCount = {};
let totalPerKomoditas = {};

filtered.forEach(item=>{

let kg = konversiKeKg(item);

totalPanen += kg;

komoditasCount[item.komoditas] = (komoditasCount[item.komoditas] || 0) + 1;

totalPerKomoditas[item.komoditas] = (totalPerKomoditas[item.komoditas] || 0) + kg;

});

document.getElementById("totalData").innerText = totalData;

// tampil otomatis
if(totalPanen >= 1000){
document.getElementById("totalPanen").innerText = (totalPanen/1000).toFixed(2) + " Ton";
}else{
document.getElementById("totalPanen").innerText = totalPanen + " Kg";
}

// komoditas terbanyak
let max = 0;
let komoditasTerbanyak = "-";

for(let k in komoditasCount){
if(komoditasCount[k] > max){
max = komoditasCount[k];
komoditasTerbanyak = k;
}
}

document.getElementById("komoditasTerbanyak").innerText = komoditasTerbanyak;


// ===== GRAFIK 1 =====
let labels1 = Object.keys(totalPerKomoditas);
let data1 = Object.values(totalPerKomoditas);

if(chart1) chart1.destroy();

chart1 = new Chart(document.getElementById("grafikKomoditas"),{
type:'bar',
data:{
labels:labels1,
datasets:[{
label:"Total Panen (Kg)",
data:data1
}]
}
});


// ===== GRAFIK 2 =====
let labels2 = filtered.map((d,i)=> "Data " + (i+1));
let data2 = filtered.map(item => konversiKeKg(item));

if(chart2) chart2.destroy();

chart2 = new Chart(document.getElementById("grafikPanen"),{
type:'line',
data:{
labels:labels2,
datasets:[{
label:"Panen (Kg)",
data:data2
}]
}
});

}

// FILTER
document.getElementById("filterKomoditas").addEventListener("change", function(){
renderData(this.value);
});

// LOAD
renderData();
</script>

<script>
let dbLabels = <?= json_encode($labels); ?>;
let dbData = <?= json_encode($dataChart); ?>;

new Chart(document.getElementById("grafikDatabase"),{
    type:'bar',
    data:{
        labels:dbLabels,
        datasets:[{
            label:"Total Panen (Kg)",
            data:dbData
        }]
    }
});
</script>

</body>
</html>
