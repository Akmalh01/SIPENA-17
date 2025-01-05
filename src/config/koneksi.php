<?php
function connectDB()
{
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'sipena';

    $conn = mysqli_connect($host, $user, $password, $database);

    if (!$conn) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
    return $conn;
}
