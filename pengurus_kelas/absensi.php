<?php
require_once '../controller/check.php';
checkAccess('pengurus_kelas');
include '../controller/pengurus_kelas.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_absensi'])) {
    $id_kelas = $_SESSION['id_kelas'];
    $tanggal = $_POST['tanggal'];
    $dibuat_oleh = $_SESSION['id_user'];

    $absensiStatus = '';
    $absensiMessage = '';

    $id_absensi = addAbsensi($id_kelas, $tanggal, $dibuat_oleh);

    if ($id_absensi) {
        $absensiStatus = 'success';
        $absensiMessage = 'Absensi berhasil dibuat dengan ID: ' . $id_absensi;
    } else {
        $absensiStatus = 'error';
        $absensiMessage = 'Gagal membuat absensi. Silakan coba lagi.';
    }
}

$id_kelas = $_SESSION['id_kelas'];
$absensiList = getAbsensiByKelas($id_kelas);

$detailStatus = '';
$detailMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_detail_absensi'])) {
    $id_absensi = $_POST['id_absensi'];
    $nama_siswa = $_POST['nama_siswa'];
    $keterangan = $_POST['keterangan'];

    if (addDetailAbsensi($id_absensi, $nama_siswa, $keterangan)) {
        updateAbsensiStatus($id_absensi, 'completed');
        $detailStatus = 'success';
        $detailMessage = 'Detail absensi berhasil ditambahkan.';
    } else {
        $detailStatus = 'error';
        $detailMessage = 'Gagal menambahkan detail absensi.';
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id_absensi'])) {
    $id_absensi = $_GET['id_absensi'];

    if (deleteAbsensi($id_absensi)) {
        header("Location: absensi.php");
    } else {
        header("Location: absensi.php");
    }
    exit;
}

$conn = connectDB();
$query = "SELECT id_absensi, tanggal, status FROM absensi WHERE id_kelas = $id_kelas";
$result = mysqli_query($conn, $query);

$absensiList = [];
while ($row = mysqli_fetch_assoc($result)) {
    $absensiList[] = $row;
}

$pendingAbsensiList = array_filter($absensiList, function ($absensi) {
    return isset($absensi['status']) && $absensi['status'] === 'pending';
});
$conn = connectDB();
$absensiPending = getAbsensiPendingByKelas($id_kelas);
$absensiCompleted = getAbsensiCompletedByKelas($id_kelas);
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 10; // Jumlah data per halaman

$totalAbsensi = getTotalAbsensi($id_kelas);
$pagination = getPagination($totalAbsensi, $itemsPerPage, $currentPage);
$offset = ($currentPage - 1) * $itemsPerPage;
$absensiData = getAllAbsensiWithDetails($id_kelas, $itemsPerPage, $offset);
$no = $offset + 1;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengurus Kelas - Absensi</title>
    <link rel="stylesheet" href="../src/css/output.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<body>
    <?php include '../components/sidebar_pk.php'; ?>
    <!-- Container -->
    <div class="p-4 pt-20 sm:ml-64">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Absensi Siswa</h1>
                <p class="text-gray-600 mt-1">Mencatat Ketidakhadiran Siswa.</p>
            </div>
            <div class="flex space-x-2">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 flex items-center" data-modal-target="absensi-modal" data-modal-toggle="absensi-modal">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Absensi
                </button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 flex items-center" data-modal-target="detail-absensi-modal" data-modal-toggle="detail-absensi-modal">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Detail Absensi
                </button>
            </div>
        </div>

        <!-- Search dan Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="bg-white p-4 rounded-lg shadow-md w-full">
                    <h2 class="text-gray-700 font-semibold">Absen Tertunda</h2>
                    <p class="text-blue-600 text-xl font-bold"><?= htmlspecialchars($absensiPending); ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md w-full">
                    <h2 class="text-gray-700 font-semibold">Absen Selesai</h2>
                    <p class="text-green-600 text-xl font-bold"><?= htmlspecialchars($absensiCompleted); ?></p>
                </div>
            </div>
            <div class="relative">
                <input type="text" placeholder="Cari absensi..." class="px-4 py-2 w-full rounded-lg shadow-md border-gray-300 focus:ring focus:ring-blue-200">
                <button class="absolute top-0 right-0 px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                    Cari
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">No</th>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Hari & Tanggal</th>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Status Absen</th>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Detail Absen</th>
                            <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        setlocale(LC_TIME, 'id_ID.UTF-8');

                        function getHariIndonesia($tanggal)
                        {
                            $hariInggris = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

                            $hari = date('l', strtotime($tanggal));
                            return str_replace($hariInggris, $hariIndonesia, $hari);
                        }

                        $no = 1;
                        foreach ($absensiData as $id_absensi => $absensi) :
                            $tanggal = getHariIndonesia($absensi['tanggal']) . ', ' . date('d F Y', strtotime($absensi['tanggal']));
                            $status = ucfirst($absensi['status']);
                        ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700"><?= $no++ ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500"><?= $tanggal ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500"><?= $status ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center" data-modal-toggle="detailModal<?= $id_absensi ?>" data-modal-target="detailModal<?= $id_absensi ?>">Lihat Detail</button>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-500">
                                    <button type="button" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-700 flex items-center" onclick="confirmDelete(<?= $id_absensi ?>)">Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Detail -->
        <?php foreach ($absensiData as $id_absensi => $absensi) : ?>
            <div id="detailModal<?= $id_absensi ?>" tabindex="-1" aria-hidden="true" class="fixed top-20 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto max-h-full">
                <div class="relative w-full max-w-2xl mx-auto">
                    <div class="relative bg-white rounded-lg shadow">
                        <div class="flex justify-between items-start p-4 border-b rounded-t">
                            <h3 class="text-xl font-semibold">
                                Detail Absensi - <?= getHariIndonesia($absensi['tanggal']) . ', ' . date('d F Y', strtotime($absensi['tanggal'])); ?>
                            </h3>
                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" data-modal-hide="detailModal<?= $id_absensi ?>">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a1 1 0 01.293-.707l5-5a1 1 0 111.414 1.414L11.414 9l5 5a1 1 0 01-1.414 1.414l-5-5-5 5a1 1 0 01-1.414-1.414l5-5L3.293 3.707A1 1 0 013 3z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4">
                            <?php if (!empty($absensi['details'])) : ?>
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2">Nama Siswa</th>
                                            <th class="px-4 py-2">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($absensi['details'] as $detail) : ?>
                                            <tr class="bg-white border-b">
                                                <td class="px-4 py-2"><?= $detail['nama_siswa'] ?></td>
                                                <td class="px-4 py-2"><?= ucfirst($detail['keterangan']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else : ?>
                                <p class="text-gray-500">Tidak ada detail absensi.</p>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center justify-end p-4 border-t">
                            <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded" data-modal-hide="detailModal<?= $id_absensi ?>">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- modal absensi -->
        <div id="absensi-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Header Modal -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="absensiModalLabel">Buat Absensi</h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="absensi-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Body Modal -->
                    <form class="p-4 md:p-5" method="POST">
                        <div class="grid gap-4 mb-4">
                            <div>
                                <label for="tanggal" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Absensi:</label>
                                <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" id="tanggal" name="tanggal" required>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="text-gray-500 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 dark:bg-gray-600 dark:hover:bg-gray-500" data-modal-toggle="absensi-modal">Tutup</button>
                            <button type="submit" name="save_absensi" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- modal detail absensi -->
        <div id="detail-absensi-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Header Modal -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="detailAbsensiModalLabel">Tambah Detail Absensi</h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="detail-absensi-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Body Modal -->
                    <form class="p-4 md:p-5" method="POST">
                        <div class="grid gap-4 mb-4">
                            <div>
                                <label for="id_absensi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Absensi:</label>
                                <select name="id_absensi" id="id_absensi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                                    <option value="" disabled selected>Pilih Absensi</option>
                                    <?php foreach ($pendingAbsensiList as $absensi): ?>
                                        <option value="<?= $absensi['id_absensi'] ?>">
                                            <?= "Absensi ID: {$absensi['id_absensi']} - Tanggal: {$absensi['tanggal']}" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div id="form-container">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 form-row">
                                    <div>
                                        <label for="nama_siswa[]" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Siswa:</label>
                                        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" name="nama_siswa[]" placeholder="Nama Siswa">
                                    </div>
                                    <div>
                                        <label for="keterangan[]" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan:</label>
                                        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" name="keterangan[]">
                                            <option value="" disabled selected>Pilih Keterangan</option>
                                            <option value="izin">Izin</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="alpha">Alpha</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-500" onclick="addFormRow()">Tambah Siswa</button>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="text-gray-500 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 dark:bg-gray-600 dark:hover:bg-gray-500" data-modal-toggle="detail-absensi-modal">Tutup</button>
                            <button type="submit" name="save_detail_absensi" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center">
            <p class="text-sm text-gray-600">
                Menampilkan <?= ($pagination['currentPage'] - 1) * $itemsPerPage + 1; ?>-
                <?= min($pagination['currentPage'] * $itemsPerPage, $totalAbsensi); ?>
                dari <?= $totalAbsensi; ?> data
            </p>
            <nav class="inline-flex -space-x-px">
                <?php if ($pagination['hasPrev']): ?>
                    <a href="?page=<?= $pagination['currentPage'] - 1; ?>" class="px-3 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <a href="?page=<?= $i; ?>" class="px-3 py-2 border <?= $i === $pagination['currentPage'] ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-100'; ?>">
                        <?= $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['hasNext']): ?>
                    <a href="?page=<?= $pagination['currentPage'] + 1; ?>" class="px-3 py-2 border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">Selanjutnya</a>
                <?php endif; ?>
            </nav>
        </div>

    </div>
    <script src="../node_modules/flowbite/dist/flowbite.min.js"></script>
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.querySelector('input[placeholder="Cari absensi..."]');
            const tableRows = document.querySelectorAll("table tbody tr");

            searchInput.addEventListener("input", () => {
                const searchTerm = searchInput.value.toLowerCase();

                tableRows.forEach(row => {
                    const cells = Array.from(row.querySelectorAll("td"));
                    const rowText = cells.map(cell => cell.textContent.toLowerCase()).join(" ");
                    if (rowText.includes(searchTerm)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        });

        function confirmDelete(id_absensi) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `absensi.php?action=delete&id_absensi=${id_absensi}`;
                }
            });
        }
    </script>
    <script>
        function addFormRow() {
            const container = document.getElementById('form-container');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3', 'form-row');
            newRow.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label for="nama_siswa[]" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Siswa:</label>
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" name="nama_siswa[]" placeholder="Nama Siswa" required>
    </div>
    <div>
        <label for="keterangan[]" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Keterangan:</label>
        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white" name="keterangan[]" required>
            <option value="" disabled selected>Pilih Keterangan</option>
            <option value="izin">Izin</option>
            <option value="sakit">Sakit</option>
            <option value="alpha">Alpha</option>
        </select>
    </div>
</div>
    `;
            container.appendChild(newRow);
        }

        function removeFormRow(button) {
            const row = button.closest('.form-row');
            row.remove();
        }
        const buttonContainer = document.querySelector('button[onclick="addFormRow()"').parentElement;
        const removeAllButton = document.createElement('button');
        removeAllButton.type = 'button';
        removeAllButton.className = 'btn btn-danger';
        removeAllButton.textContent = 'Hapus Semua';
        removeAllButton.onclick = () => {
            document.getElementById('form-container').innerHTML = '';
        };
        buttonContainer.appendChild(removeAllButton);
    </script>
    <?php if (isset($absensiStatus) && $absensiStatus === 'success'): ?>
        <script>
            Swal.fire({
                title: "Berhasil!",
                text: "<?= $absensiMessage ?>",
                icon: "success"
            }).then(() => {
                window.location.href = 'absensi.php';
            });
        </script>
    <?php elseif (isset($absensiStatus) && $absensiStatus === 'error'): ?>
        <script>
            Swal.fire({
                title: "Gagal!",
                text: "<?= $absensiMessage ?>",
                icon: "error"
            });
        </script>
    <?php endif; ?>

    <?php if (isset($detailStatus) && $detailStatus === 'success'): ?>
        <script>
            Swal.fire({
                title: "Berhasil!",
                text: "<?= $detailMessage ?>",
                icon: "success"
            }).then(() => {
                window.location.href = 'absensi.php';
            });
        </script>
    <?php elseif (isset($detailStatus) && $detailStatus === 'error'): ?>
        <script>
            Swal.fire({
                title: "Gagal!",
                text: "<?= $detailMessage ?>",
                icon: "error"
            });
        </script>
    <?php endif; ?>
</body>

</html>