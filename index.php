<?php
session_start();

function checkLoginAndRedirect()
{
    if (isset($_SESSION['id_user'], $_SESSION['role'])) {
        $role = $_SESSION['role'];

        switch ($role) {
            case 'admin':
                header('Location: admin/dashboard.php');
                exit;
            case 'resepsionis':
                header('Location: resepsionis/absensi.php');
                exit;
            case 'pengurus_kelas':
                header('Location: pengurus_kelas/absensi.php');
                exit;
            default:
                session_destroy();
                header('Location: login.php');
                exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}

checkLoginAndRedirect();
