<?php
include '../src/config/koneksi.php';

function logAktivitas($id_user, $aktivitas)
{
    $conn = connectDB();
    $query = "INSERT INTO log_akses (id_user, aktivitas) VALUES ('$id_user', '$aktivitas')";
    mysqli_query($conn, $query);
    mysqli_close($conn);
}

// Kelas
function generateIdKelas()
{
    $prefix = '17';
    $randomNumber = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    return $prefix . $randomNumber;
}

function createKelas($kelas, $nama_kelas, $jurusan, $nama_wali_kelas)
{
    $conn = connectDB();
    $id_kelas = generateIdKelas();

    $existingCheck = mysqli_query($conn, "SELECT id_kelas FROM kelas WHERE id_kelas = '$id_kelas'");
    while (mysqli_num_rows($existingCheck) > 0) {
        $id_kelas = generateIdKelas();
        $existingCheck = mysqli_query($conn, "SELECT id_kelas FROM kelas WHERE id_kelas = '$id_kelas'");
    }

    $sql = "INSERT INTO kelas (id_kelas, kelas, nama_kelas, jurusan, nama_wali_kelas) VALUES ('$id_kelas', '$kelas', '$nama_kelas', '$jurusan', '$nama_wali_kelas')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        session_start();
        $id_user = $_SESSION['id_user'];
        logAktivitas($id_user, 'Membuat kelas');
    }

    mysqli_close($conn);
    return $result;
}


function readKelas()
{
    $conn = connectDB();
    $sql = "SELECT * FROM kelas";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_close($conn);
    return $rows;
}

function updateKelas($id_kelas, $kelas, $nama_kelas, $jurusan, $nama_wali_kelas)
{
    $conn = connectDB();
    $sql = "UPDATE kelas SET kelas = '$kelas', nama_kelas = '$nama_kelas', jurusan = '$jurusan', nama_wali_kelas = '$nama_wali_kelas' WHERE id_kelas = $id_kelas";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        session_start();
        $id_user = $_SESSION['id_user'];
        logAktivitas($id_user, 'Mengupdate kelas');
    }

    mysqli_close($conn);
    return $result;
}


function deleteKelas($id_kelas)
{
    $conn = connectDB();
    $sql = "DELETE FROM kelas WHERE id_kelas = $id_kelas";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        session_start();
        $id_user = $_SESSION['id_user'];
        logAktivitas($id_user, 'Menghapus kelas');
    }

    mysqli_close($conn);
    return $result;
}


// pengguna
function readUsers()
{
    $conn = connectDB();
    $sql = "SELECT user.*, kelas.nama_kelas FROM user LEFT JOIN kelas ON user.id_kelas = kelas.id_kelas";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_close($conn);
    return $rows;
}

function createUser($username, $password, $email, $full_name, $role, $id_kelas)
{
    $conn = connectDB();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $idKelasValue = empty($id_kelas) ? 'NULL' : $id_kelas;

    $sql = "INSERT INTO user (username, password, email, full_name, role, id_kelas) 
            VALUES ('$username', '$passwordHash', '$email', '$full_name', '$role', $idKelasValue)";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Error on createUser: " . mysqli_error($conn));
    }

    session_start();
    $id_user = $_SESSION['id_user'];
    logAktivitas($id_user, 'Membuat user baru');

    mysqli_close($conn);
    return $result;
}

function updateUser($id_user, $username, $password, $email, $full_name, $role, $id_kelas)
{
    $conn = connectDB();
    $setPassword = $password ? ", password = '" . password_hash($password, PASSWORD_DEFAULT) . "'" : "";
    $id_kelas = !empty($id_kelas) ? $id_kelas : 'NULL';

    $sql = "UPDATE user 
            SET username = '$username', email = '$email', full_name = '$full_name', role = '$role', id_kelas = $id_kelas
                $setPassword
            WHERE id_user = $id_user";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        session_start();
        $id_user = $_SESSION['id_user'];
        logAktivitas($id_user, 'Mengupdate user');
    }

    mysqli_close($conn);
    return $result;
}

function deleteUser($id_user)
{
    $conn = connectDB();
    $sql = "DELETE FROM user WHERE id_user = $id_user";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        session_start();
        $id_logged_in_user = $_SESSION['id_user'];
        logAktivitas($id_logged_in_user, 'Menghapus user');
    }

    mysqli_close($conn);
    return $result;
}

// aktivitas
function getTotalAktivitas($conn)
{
    $query = "SELECT COUNT(*) AS total_aktivitas FROM log_akses";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_aktivitas'];
    }
    return 0;
}

function getUserAktif($conn)
{
    $query = "SELECT COUNT(DISTINCT id_user) AS total_user_aktif FROM log_akses WHERE waktu >= NOW() - INTERVAL 1 DAY";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_user_aktif'];
    }
    return 0;
}

function getLogAkses($conn, $limit, $offset)
{
    $query = "SELECT * FROM log_akses ORDER BY waktu DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);
    $logs = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
    }
    return $logs;
}

function getPagination($totalItems, $itemsPerPage, $currentPage)
{
    $totalPages = ceil($totalItems / $itemsPerPage);
    $pagination = [
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'hasPrev' => $currentPage > 1,
        'hasNext' => $currentPage < $totalPages,
    ];
    return $pagination;
}

function getTotalUser($conn)
{
    $query = "SELECT COUNT(*) AS total_user FROM user";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_user'];
    }
    return 0;
}

function getTotalKelas($conn)
{
    $query = "SELECT COUNT(*) AS total_kelas FROM kelas";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_kelas'];
    }
    return 0;
}

function getRecentLogAktivitas($conn, $limit = 5)
{
    $query = "SELECT log_akses.id_log, log_akses.id_user, log_akses.aktivitas, log_akses.waktu, user.full_name 
              FROM log_akses
              JOIN user ON log_akses.id_user = user.id_user
              ORDER BY log_akses.waktu DESC
              LIMIT $limit";

    $result = mysqli_query($conn, $query);

    $logs = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $logs[] = $row;
        }
    }
    return $logs;
}

// pengaturan
function updateUserSettings($id_user, $full_name, $email, $password = null)
{
    $conn = connectDB();
    $setPassword = $password ? ", password = '" . password_hash($password, PASSWORD_DEFAULT) . "'" : "";

    $sql = "UPDATE user 
            SET full_name = '$full_name', email = '$email'
                $setPassword
            WHERE id_user = $id_user";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        logAktivitas($id_user, 'Memperbarui akun admin');
    }

    mysqli_close($conn);
    return $result;
}
