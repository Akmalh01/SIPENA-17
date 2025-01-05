<?php
require_once '../controller/check.php';
checkAccess('admin');
include '../controller/admin.php';
$conn = connectDB();
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$totalAktivitas = getTotalAktivitas($conn);
$userAktif = getUserAktif($conn);
$logs = getLogAkses($conn, $itemsPerPage, $offset);
$pagination = getPagination($totalAktivitas, $itemsPerPage, $currentPage);

mysqli_close($conn);
?>

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Aktivitas</title>
    <link rel="stylesheet" href="../src/css/output.css">
</head>

<body>
    <?php include '../components/sidebar_admin.php'; ?>
    <!-- Container -->
    <div class="p-4 pt-20 sm:ml-64">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Aktivitas User</h1>
                <p class="text-gray-600 mt-1">Pantau semua aktivitas yang dilakukan oleh user di sistem.</p>
            </div>

        </div>

        <!-- Search dan Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="bg-white p-4 rounded-lg shadow-md w-full">
                    <h2 class="text-gray-700 font-semibold">Total Aktivitas</h2>
                    <p class="text-blue-600 text-xl font-bold"><?= htmlspecialchars($totalAktivitas); ?></p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow-md w-full">
                    <h2 class="text-gray-700 font-semibold">User Aktif</h2>
                    <p class="text-green-600 text-xl font-bold"><?= htmlspecialchars($userAktif); ?></p>
                </div>
            </div>
            <div class="relative">
                <input type="text" placeholder="Cari aktivitas..." class="px-4 py-2 w-full rounded-lg shadow-md border-gray-300 focus:ring focus:ring-blue-200">
                <button class="absolute top-0 right-0 px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                    Cari
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">No</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">User ID</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Aktivitas</th>
                        <th class="px-4 py-2 text-left text-sm font-bold text-gray-500 uppercase">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($logs)) : ?>
                        <?php foreach ($logs as $index => $log) : ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700"><?= htmlspecialchars($index + 1 + $offset); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500"><?= htmlspecialchars($log['id_user']); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500 capitalize"><?= htmlspecialchars($log['aktivitas']); ?></td>
                                <td class="px-4 py-2 text-sm text-gray-500"><?= htmlspecialchars($log['waktu']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">Tidak ada aktivitas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center">
            <p class="text-sm text-gray-600">Menampilkan <?= htmlspecialchars($offset + 1); ?>-<?= htmlspecialchars(min($offset + $itemsPerPage, $totalAktivitas)); ?> dari <?= htmlspecialchars($totalAktivitas); ?> data</p>
            <nav class="inline-flex -space-x-px">
                <?php if ($pagination['hasPrev']) : ?>
                    <a href="?page=<?= $pagination['currentPage'] - 1; ?>" class="px-3 py-2 rounded-l-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++) : ?>
                    <a href="?page=<?= $i; ?>" class="px-3 py-2 border border-gray-300 <?= $i === $pagination['currentPage'] ? 'bg-blue-600 text-white' : 'bg-white text-gray-500 hover:bg-gray-100'; ?>">
                        <?= $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($pagination['hasNext']) : ?>
                    <a href="?page=<?= $pagination['currentPage'] + 1; ?>" class="px-3 py-2 rounded-r-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-100">Selanjutnya</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
    <script src="../node_modules/flowbite/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.querySelector('input[placeholder="Cari aktivitas..."]');
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
    </script>
</body>

</html>