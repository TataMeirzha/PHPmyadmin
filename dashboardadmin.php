<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Koneksi database
$conn = new mysqli("localhost", "root", "", "panen_db");

// Hitung jumlah data (contoh)
$user = $conn->query("SELECT * FROM user")->num_rows;
$panen = $conn->query("SELECT * FROM panen_user")->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            margin: 10px 0;
            text-decoration: none;
            background: #34495e;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: #1abc9c;
        }

        /* Content */
        .content {
            flex: 1;
            padding: 20px;
            background: #ecf0f1;
        }

        .card {
            display: inline-block;
            width: 200px;
            padding: 20px;
            margin: 10px;
            background: white;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .card h3 {
            margin: 0;
        }

        .logout {
            background: red !important;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="login.php">Data User</a>
        <a href="PencatatanPanen.php">Data Panen</a>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Selamat Datang, <?php echo $_SESSION['username']; ?> 👋</h1>

        <div class="card">
            <h3>Total User</h3>
            <p><?php echo $username; ?></p>
        </div>

        <div class="card">
            <h3>Total Data Panen</h3>
            <p><?php echo $panen; ?></p>
        </div>

    </div>

</body>
</html>