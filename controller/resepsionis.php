<?php
require_once '../src/config/koneksi.php';

function logAktivitas($id_user, $aktivitas)
{
    $conn = connectDB();
    $query = "INSERT INTO log_akses (id_user, aktivitas) VALUES ('$id_user', '$aktivitas')";
    mysqli_query($conn, $query);
    mysqli_close($conn);
}

function getAllAbsensiByDate($tanggal)
{
    $conn = connectDB();
    $query = "
        SELECT k.id_kelas, k.nama_kelas, a.id_absensi, a.tanggal, a.status
        FROM kelas k
        LEFT JOIN absensi a ON k.id_kelas = a.id_kelas AND a.tanggal = '$tanggal'
        ORDER BY k.nama_kelas ASC
    ";
    $result = mysqli_query($conn, $query);
    $absensiData = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $absensiData[] = [
            'id_kelas' => $row['id_kelas'],
            'nama_kelas' => $row['nama_kelas'],
            'id_absensi' => $row['id_absensi'],
            'tanggal' => $row['tanggal'],
            'status' => $row['status'] ?? 'Not Sent',
        ];
    }

    mysqli_close($conn);
    return $absensiData;
}
function getAllKelas()
{
    $conn = connectDB();
    $query = "SELECT * FROM kelas ORDER BY kelas, nama_kelas";
    $result = $conn->query($query);

    $kelas = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $kelas[] = $row;
        }
    }

    $conn->close();
    return $kelas;
}

function getAbsensiByKelasAndTanggal($id_kelas, $tanggal)
{
    $conn = connectDB();
    $query = "
        SELECT da.nama_siswa, da.keterangan
        FROM absensi AS a
        JOIN detail_absensi AS da ON a.id_absensi = da.id_absensi
        WHERE a.id_kelas = $id_kelas AND a.tanggal = '$tanggal'
    ";

    $result = $conn->query($query);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $conn->close();
    return $data;
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
        logAktivitas($id_user, 'Memperbarui akun resepsionis');
    }

    mysqli_close($conn);
    return $result;
}
