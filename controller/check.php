<?php
session_start();

function checkAccess($requiredRole)
{
    if (!isset($_SESSION['id_user']) || !isset($_SESSION['role'])) {
        header('Location: ../login.php');
        exit;
    }

    if ($_SESSION['role'] !== $requiredRole) {
        echo "<script>
            alert('Anda tidak memiliki akses ke halaman ini!');
            window.location.href = '../login.php';
        </script>";
        session_destroy();
        exit;
    }
}
