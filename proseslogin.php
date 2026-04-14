<?php
session_start();
include "koneksi.php";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $pass = $_POST['password'];

    // ambil data user
    $query = $conn->query("SELECT * FROM user WHERE username='$username'");
    $data = $query->fetch_assoc();

    if($data && password_verify($pass, $data['password'])){
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;

        // redirect sesuai role
        if($data['role'] == 'admin'){
            header("Location: dashboardadmin.php"); // pastikan file ada
        } else {
            header("Location: PencatatanPanen.php");
        }
        exit;

    } else {
        echo "Login gagal! Username atau password salah";
    }
}
?>