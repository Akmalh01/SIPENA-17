<?php
require_once '../controller/check.php';
checkAccess('admin');
include '../controller/admin.php';
$conn = connectDB();
$totalAktivitas = getTotalAktivitas($conn);
$totalKelas = getTotalKelas($conn);
$totalUser = getTotalUser($conn);
$recentLogs = getRecentLogAktivitas($conn, 5);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="../src/css/output.css">
</head>

<body>
    <?php include '../components/sidebar_admin.php'; ?>
    <!-- Container -->
    <div class="p-4 pt-20 sm:ml-64">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard Admin</h1>
            <p class="text-sm sm:text-base text-gray-600 mt-1">Pantau semua data sistem secara ringkas.</p>
        </div>

        <!-- Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <!-- Total pengguna -->
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md flex items-center">
                <div class="bg-blue-100 text-blue-600 p-2 sm:p-3 rounded-full">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42a4 4 0 110-7.16L12 5m0 9v5"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm sm:text-base text-gray-700 font-semibold">Total User</h2>
                    <p class="text-lg sm:text-2xl font-bold text-blue-600"><?= $totalUser; ?></p>
                </div>
            </div>

            <!-- Total kelas -->
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md flex items-center">
                <div class="bg-green-100 text-green-600 p-2 sm:p-3 rounded-full">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.42a4 4 0 110-7.16L12 5m0 9v5"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm sm:text-base text-gray-700 font-semibold">Total Kelas</h2>
                    <p class="text-lg sm:text-2xl font-bold text-green-600"><?= $totalKelas; ?></p>
                </div>
            </div>

            <!-- Total aktivitas -->
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md flex items-center">
                <div class="bg-yellow-100 text-yellow-600 p-2 sm:p-3 rounded-full">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h11m-6 4h7M9 21h6"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7V4m0 3v3"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-sm sm:text-base text-gray-700 font-semibold">Total Aktivitas</h2>
                    <p class="text-lg sm:text-2xl font-bold text-yellow-600"><?= $totalAktivitas; ?></p>
                </div>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="mt-8">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">Log Aktivitas Terbaru</h2>
            <div class="mt-4 bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wide">No</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wide">Nama User</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wide">Aktivitas</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wide">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($recentLogs)) : ?>
                            <?php foreach ($recentLogs as $index => $log) : ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 sm:px-6 py-2 text-xs sm:text-sm font-medium text-gray-700">
                                        <?php echo $index + 1; ?>
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 text-xs sm:text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log['full_name']); ?>
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 text-xs sm:text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log['aktivitas']); ?>
                                    </td>
                                    <td class="px-3 sm:px-6 py-2 text-xs sm:text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log['waktu']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="px-3 sm:px-6 py-2 text-center text-xs sm:text-sm text-gray-500">
                                    Tidak ada aktivitas terbaru.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>