<?php
function log_login($id_user, $conn)
{
    $query = "INSERT INTO log_akses (id_user, aktivitas, waktu) VALUES ('$id_user', 'Login', NOW())";
    mysqli_query($conn, $query);
}

function loginUser($username, $password)
{
    $conn = connectDB();

    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'pengurus_kelas') {
                $_SESSION['id_kelas'] = $user['id_kelas'];
            }

            log_login($user['id_user'], $conn);

            // Redirect sesuai role
            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'resepsionis':
                    header('Location: resepsionis/absensi.php');
                    break;
                case 'pengurus_kelas':
                    header('Location: pengurus_kelas/absensi.php');
                    break;
                default:
                    session_destroy();
                    echo "<script>
                        alert('Role pengguna tidak valid.');
                        window.location.href = 'login.php';
                    </script>";
                    break;
            }
            exit;
        } else {
            echo "<script>
                alert('Password salah!');
                window.location.href = 'login.php';
            </script>";
        }
    } else {
        echo "<script>
            alert('Username tidak ditemukan!');
            window.location.href = 'login.php';
        </script>";
    }
    mysqli_close($conn);
}
