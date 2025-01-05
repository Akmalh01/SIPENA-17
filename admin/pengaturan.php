<?php
require_once '../controller/check.php';
checkAccess('admin');
include '../controller/admin.php';

$status = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = $_SESSION['id_user'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'] ?? null;

    if (updateUserSettings($id_user, $full_name, $email, $password)) {
        $status = 'success';
        $message = 'Data berhasil diperbarui!';
    } else {
        $status = 'error';
        $message = 'Gagal memperbarui data.';
    }
}

$id_user = $_SESSION['id_user'];
$conn = connectDB();
$result = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id_user'");
$userData = mysqli_fetch_assoc($result);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pengaturan</title>
    <link rel="stylesheet" href="../src/css/output.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<body>
    <?php include '../components/sidebar_admin.php'; ?>
    <!-- Container -->
    <div class="p-4 pt-20 sm:ml-64">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Pengaturan</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Atur informasi akun Anda.</p>
        </div>

        <!-- Profile Settings -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Pengaturan Profil</h2>
            <form action="" method="POST" class="mt-6 space-y-4">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" name="full_name" id="full_name" value="<?= htmlspecialchars($userData['full_name']); ?>" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($userData['email']); ?>" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password baru (opsional)" class="w-full mt-1 p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        <?php if ($status === 'success'): ?>
            Swal.fire({
                title: "Good job!",
                text: "<?= $message ?>",
                icon: "success"
            }).then(() => window.location = 'pengaturan.php');
        <?php elseif ($status === 'error'): ?>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "<?= $message ?>"
            }).then(() => window.location = 'pengaturan.php');
        <?php endif; ?>
    </script>
</body>

</html>