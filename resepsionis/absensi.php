<?php
require_once '../controller/check.php';
checkAccess('resepsionis');
include '../controller/resepsionis.php';

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$absensiData = getAllAbsensiByDate($tanggal);

function getHariIndonesia($tanggal)
{
    $hariInggris = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    $hari = date('l', strtotime($tanggal));
    return str_replace($hariInggris, $hariIndonesia, $hari);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resepsionis - Absensi</title>
    <link rel="stylesheet" href="../src/css/output.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<body>
    <?php include '../components/sidebar_resepsionis.php'; ?>
    <!-- Container -->
    <div class="p-4 pt-20 sm:ml-64">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Absensi Kelas</h1>
                <p class="text-gray-600 mt-1">Memantau absensi setiap kelas setiap hari.</p>
            </div>
            <form method="GET" class="flex items-center space-x-4">
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" class="p-2 border rounded-lg shadow-md border-gray-300 focus:ring focus:ring-blue-200">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700">Filter</button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Kelas</th>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Hari & Tanggal</th>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Status Absen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if ($absensiData) : ?>
                            <?php foreach ($absensiData as $index => $absensi) : ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700"><?= $index + 1 ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500"><?= htmlspecialchars($absensi['nama_kelas']) ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-500">
                                        <?php if ($absensi['tanggal']) : ?>
                                            <?= getHariIndonesia($absensi['tanggal']) . ', ' . date('d F Y', strtotime($absensi['tanggal'])) ?>
                                        <?php else : ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-500">
                                        <?= htmlspecialchars($absensi['status']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-sm text-gray-500 text-center">Tidak ada data absensi untuk tanggal ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="../node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="../node_modules/flowbite/dist/flowbite.min.js"></script>
</body>

</html>