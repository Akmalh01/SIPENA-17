<?php
require_once '../src/config/koneksi.php';

function logAktivitas($id_user, $aktivitas)
{
    $conn = connectDB();
    $query = "INSERT INTO log_akses (id_user, aktivitas) VALUES ('$id_user', '$aktivitas')";
    mysqli_query($conn, $query);
    mysqli_close($conn);
}

function addAbsensi($id_kelas, $tanggal, $dibuat_oleh)
{
    $conn = connectDB();

    $query = "INSERT INTO absensi (id_kelas, tanggal, dibuat_oleh) 
              VALUES ($id_kelas, '$tanggal', $dibuat_oleh)";

    if (mysqli_query($conn, $query)) {
        $id_absensi = mysqli_insert_id($conn);
        mysqli_close($conn);
        return $id_absensi;
    } else {
        mysqli_close($conn);
        return false;
    }
}

function updateAbsensiStatus($id_absensi, $status)
{
    $conn = connectDB();

    // Query untuk memperbarui status absensi
    $query = "UPDATE absensi SET status = '$status' WHERE id_absensi = $id_absensi";

    $result = mysqli_query($conn, $query);
    mysqli_close($conn);

    return $result;
}


function addDetailAbsensi($id_absensi, $nama_siswa, $keterangan)
{
    $conn = connectDB();


    foreach ($nama_siswa as $index => $nama) {
        $nama_siswa_kosong = mysqli_real_escape_string($conn, $nama ?: 'nihil');
        $keterangan_kosong = mysqli_real_escape_string($conn, $keterangan[$index] ?: 'nihil');

        $query = "INSERT INTO detail_absensi (id_absensi, nama_siswa, keterangan) 
                  VALUES ('$id_absensi', '$nama_siswa_kosong', '$keterangan_kosong')";

        if (!mysqli_query($conn, $query)) {
            mysqli_close($conn);
            return false;
        }
    }

    mysqli_close($conn);
    return true;
}


function getAbsensiByKelas($id_kelas)
{
    $conn = connectDB();
    $sql = "SELECT id_absensi, tanggal FROM absensi WHERE id_kelas = $id_kelas";
    $result = mysqli_query($conn, $sql);

    $absensiList = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $absensiList[] = $row;
    }

    mysqli_close($conn);
    return $absensiList;
}

function getIdKelas($user_id)
{
    $conn = connectDB();
    $query = "SELECT id_kelas FROM user WHERE id_user = $user_id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query gagal: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);
    mysqli_close($conn);

    return $user['id_kelas'] ?? null;
}

function getAbsenByKelas($id_kelas)
{
    $conn = connectDB();
    $query = "SELECT DATE(dibuat_pada) as tanggal, COUNT(*) as jumlah_absensi 
              FROM absensi WHERE id_kelas = $id_kelas GROUP BY DATE(dibuat_pada)";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query gagal: " . mysqli_error($conn));
    }

    $absensi = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $absensi[] = $row;
    }

    mysqli_close($conn);
    return $absensi;
}

function getAbsensiPendingByKelas($id_kelas)
{
    $conn = connectDB();
    $query = "SELECT COUNT(*) AS absen_pending 
              FROM absensi 
              WHERE status = 'pending' AND id_kelas = '$id_kelas'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row['absen_pending'];
    }
    mysqli_close($conn);
    return 0;
}

function getAbsensiCompletedByKelas($id_kelas)
{
    $conn = connectDB();
    $query = "SELECT COUNT(*) AS absen_completed 
              FROM absensi 
              WHERE status = 'completed' AND id_kelas = '$id_kelas'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row['absen_completed'];
    }
    mysqli_close($conn);
    return 0;
}

function getAllAbsensiWithDetails($id_kelas, $limit, $offset)
{
    $conn = connectDB();
    $query = "
        SELECT a.id_absensi, a.tanggal, a.status
        FROM absensi a
        WHERE a.id_kelas = '$id_kelas'
        ORDER BY a.tanggal DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = mysqli_query($conn, $query);
    $data = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $id_absensi = $row['id_absensi'];

        $data[$id_absensi] = [
            'id_absensi' => $row['id_absensi'],
            'tanggal' => $row['tanggal'],
            'status' => $row['status'],
            'details' => []
        ];
    }

    $absensiIds = array_keys($data);
    if (!empty($absensiIds)) {
        $absensiIdsString = implode(',', $absensiIds);
        $detailQuery = "
            SELECT d.id_absensi, d.nama_siswa, d.keterangan
            FROM detail_absensi d
            WHERE d.id_absensi IN ($absensiIdsString)
        ";
        $detailResult = mysqli_query($conn, $detailQuery);
        while ($detailRow = mysqli_fetch_assoc($detailResult)) {
            $data[$detailRow['id_absensi']]['details'][] = [
                'nama_siswa' => $detailRow['nama_siswa'],
                'keterangan' => $detailRow['keterangan']
            ];
        }
    }

    mysqli_close($conn);
    return $data;
}

function getTotalAbsensi($id_kelas)
{
    $conn = connectDB();
    $query = "SELECT COUNT(*) AS total FROM absensi WHERE id_kelas = '$id_kelas'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($conn);
    return $row['total'];
}


function getTotalAbsensiCount($id_kelas)
{
    $conn = connectDB();
    $query = "SELECT COUNT(*) as total FROM absensi WHERE id_kelas = '$id_kelas'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    mysqli_close($conn);

    return $row['total'];
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

function deleteAbsensi($id_absensi)
{
    $conn = connectDB();
    mysqli_query($conn, "DELETE FROM detail_absensi WHERE id_absensi = $id_absensi");
    $result = mysqli_query($conn, "DELETE FROM absensi WHERE id_absensi = $id_absensi");

    mysqli_close($conn);

    return $result;
}

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
        logAktivitas($id_user, 'Memperbarui akun pengurus kelas');
    }

    mysqli_close($conn);
    return $result;
}
