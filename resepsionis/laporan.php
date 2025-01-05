<?php
require_once '../controller/check.php';
checkAccess('resepsionis');
include '../controller/resepsionis.php';

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Ambil semua data kelas
$kelasList = getAllKelas();

$kelasX = array_filter($kelasList, fn($k) => $k['kelas'] === 'X');
$kelasXI = array_filter($kelasList, fn($k) => $k['kelas'] === 'XI');
$kelasXII = array_filter($kelasList, fn($k) => $k['kelas'] === 'XII');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi</title>
    <link rel="stylesheet" href="../src/css/output.css">
</head>

<body>
    <?php include '../components/sidebar_resepsionis.php'; ?>

    <!-- Container -->
    <div class="p-4 pt-20 sm:ml-64">
        <div class="container mx-auto">
            <h1 class="text-2xl sm:text-xl md:text-2xl font-bold mb-6">Laporan Absensi Harian</h1>

            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <!-- Form Filter Tanggal -->
                <form method="GET" class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <label for="tanggal" class="block text-sm font-medium">Pilih Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" value="<?php echo $tanggal; ?>" class="border rounded px-4 py-2 w-full sm:w-auto">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full sm:w-auto">
                        Filter
                    </button>
                </form>

                <!-- Button Export to Excel -->
                <form method="GET" action="export_excel.php" class="flex items-center gap-4 w-full sm:w-auto">
                    <input type="hidden" name="tanggal" value="<?php echo $tanggal; ?>">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full sm:w-auto">
                        Export to Excel
                    </button>
                </form>
            </div>

            <!-- Layout Grup Kelas -->
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Grup Kelas X -->
                <div>
                    <h2 class="text-lg sm:text-md font-bold mb-4 text-center bg-amber-400 p-2 rounded">Kelas X</h2>
                    <?php foreach ($kelasX as $kelas) : ?>
                        <?php
                        $dataAbsensi = getAbsensiByKelasAndTanggal($kelas['id_kelas'], $tanggal);
                        $jumlahBaris = max(5, count($dataAbsensi));
                        ?>
                        <div class="bg-white shadow-md rounded-lg mb-4 overflow-x-auto">
                            <h3 class="text-md font-bold p-2 bg-amber-300 text-center rounded-t-lg">
                                <?php echo $kelas['nama_kelas']; ?>
                            </h3>
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-4 py-2 text-left">Nama Siswa</th>
                                        <th class="border px-4 py-2 text-left">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < $jumlahBaris; $i++) : ?>
                                        <tr>
                                            <td class="border px-4 py-2">
                                                <?php echo isset($dataAbsensi[$i]['nama_siswa']) ? $dataAbsensi[$i]['nama_siswa'] : ''; ?>
                                            </td>
                                            <td class="border px-4 py-2">
                                                <?php echo isset($dataAbsensi[$i]['keterangan']) ? $dataAbsensi[$i]['keterangan'] : ''; ?>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Grup Kelas XI -->
                <div>
                    <h2 class="text-lg sm:text-md font-bold mb-4 text-center bg-teal-400 p-2 rounded">Kelas XI</h2>
                    <?php foreach ($kelasXI as $kelas) : ?>
                        <?php
                        $dataAbsensi = getAbsensiByKelasAndTanggal($kelas['id_kelas'], $tanggal);
                        $jumlahBaris = max(5, count($dataAbsensi));
                        ?>
                        <div class="bg-white shadow-md rounded-lg mb-4 overflow-x-auto">
                            <h3 class="text-md font-bold p-2 bg-teal-300 text-center rounded-t-lg">
                                <?php echo $kelas['nama_kelas']; ?>
                            </h3>
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-4 py-2 text-left">Nama Siswa</th>
                                        <th class="border px-4 py-2 text-left">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < $jumlahBaris; $i++) : ?>
                                        <tr>
                                            <td class="border px-4 py-2">
                                                <?php echo isset($dataAbsensi[$i]['nama_siswa']) ? $dataAbsensi[$i]['nama_siswa'] : ''; ?>
                                            </td>
                                            <td class="border px-4 py-2">
                                                <?php echo isset($dataAbsensi[$i]['keterangan']) ? $dataAbsensi[$i]['keterangan'] : ''; ?>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Grup Kelas XII -->
                <div>
                    <h2 class="text-lg sm:text-md font-bold mb-4 text-center bg-indigo-400 p-2 rounded">Kelas XII</h2>
                    <?php foreach ($kelasXII as $kelas) : ?>
                        <?php
                        $dataAbsensi = getAbsensiByKelasAndTanggal($kelas['id_kelas'], $tanggal);
                        $jumlahBaris = max(5, count($dataAbsensi));
                        ?>
                        <div class="bg-white shadow-md rounded-lg mb-4 overflow-x-auto">
                            <h3 class="text-md font-bold p-2 bg-indigo-300 text-center rounded-t-lg">
                                <?php echo $kelas['nama_kelas']; ?>
                            </h3>
                            <table class="w-full table-auto">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border px-4 py-2 text-left">Nama Siswa</th>
                                        <th class="border px-4 py-2 text-left">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < $jumlahBaris; $i++) : ?>
                                        <tr>
                                            <td class="border px-4 py-2">
                                                <?php echo isset($dataAbsensi[$i]['nama_siswa']) ? $dataAbsensi[$i]['nama_siswa'] : ''; ?>
                                            </td>
                                            <td class="border px-4 py-2">
                                                <?php echo isset($dataAbsensi[$i]['keterangan']) ? $dataAbsensi[$i]['keterangan'] : ''; ?>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../node_modules/flowbite/dist/flowbite.min.js"></script>
</body>

</html>