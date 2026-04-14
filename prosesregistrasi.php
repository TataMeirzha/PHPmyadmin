<?php
include 'koneksi.php';

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);

if (empty($username) || empty($email) || empty($password)) {
    echo "Username, Email dan Password wajib diisi!";
    exit;
}

$query = "INSERT INTO user (username, email, password, role) VALUES ('$username', '$email', '$password_hash', 'user')";
$result = mysqli_query($conn, $query);

if ($result){
        header ("Location: login.php");
    } else {
        echo "Register Gagal: " . mysqli_error($conn);
    }
?>