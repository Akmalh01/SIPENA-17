<?php
include '../src/config/koneksi.php';
function logoutUser()
{
    session_start();

    if (isset($_SESSION['id_user'])) {
        $id_user = $_SESSION['id_user'];

        $conn = connectDB();
        $query = "INSERT INTO log_akses (id_user, aktivitas, waktu) VALUES ('$id_user', 'Logout', NOW())";
        mysqli_query($conn, $query);
        mysqli_close($conn);

        session_unset();
        session_destroy();
    }

    header('Location: ../login.php');
    exit;
}

logoutUser();
