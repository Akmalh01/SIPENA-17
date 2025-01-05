<?php
require_once '../controller/check.php';
checkAccess('admin');
include '../controller/admin.php';

$action = $_POST['action'] ?? null;
$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $kelas = $_POST['kelas'];
        $nama_kelas = $_POST['nama_kelas'];
        $jurusan = $_POST['jurusan'];
        $nama_wali_kelas = $_POST['nama_wali_kelas'];
        if (createKelas($kelas, $nama_kelas, $jurusan, $nama_wali_kelas)) {
            $status = 'success';
            $message = 'Data berhasil ditambahkan!';
        } else {
            $status = 'error';
            $message = 'Gagal menambahkan data.';
        }
    } elseif ($action === 'update') {
        $id_kelas = $_POST['id_kelas'];
        $kelas = $_POST['kelas'];
        $nama_kelas = $_POST['nama_kelas'];
        $jurusan = $_POST['jurusan'];
        $nama_wali_kelas = $_POST['nama_wali_kelas'];
        if (updateKelas($id_kelas, $kelas, $nama_kelas, $jurusan, $nama_wali_kelas)) {
            $status = 'success';
            $message = 'Data berhasil diperbarui!';
        } else {
            $status = 'error';
            $message = 'Gagal memperbarui data.';
        }
    } elseif ($action === 'delete') {
        $id_kelas = $_POST['id_kelas'];
        if (deleteKelas($id_kelas)) {
            $status = 'success';
            $message = 'Data berhasil dihapus!';
        } else {
            $status = 'error';
            $message = 'Gagal menghapus data.';
        }
    }
}

$kelas = readKelas();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelas</title>
    <link rel="stylesheet" href="../src/css/output.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<body class="">
    <?php include '../components/sidebar_admin.php'; ?>
    <div class="p-4 pt-8 sm:ml-64">
        <div class="container mx-auto mt-10 px-4 sm:px-6 lg:px-8">
            <!-- Search and Filter -->
            <div class="flex flex-wrap justify-between items-center mb-4 gap-4">
                <div class="relative w-full sm:w-auto">
                    <input
                        type="text"
                        id="search"
                        placeholder="Cari..."
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <div class="w-full sm:w-auto">
                    <select
                        id="filter"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">Filter Jurusan</option>
                        <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Manajemen Perkantoran">Manajemen Perkantoran</option>
                        <option value="Bisnis Retail">Bisnis Retail</option>
                    </select>
                </div>
                <button
                    data-modal-target="crud-modal"
                    data-modal-toggle="crud-modal"
                    class="w-full sm:w-auto block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center justify-center"
                    type="button"
                    onclick="openCreateForm()">
                    <svg
                        class="w-6 h-6 mr-2 text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="none"
                        viewBox="0 0 24 24">
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 12h14m-7 7V5" />
                    </svg>
                    Tambah Data
                </button>
            </div>
            <?php if (isset($_GET['status']) && isset($_GET['message'])) : ?>
                <script>
                    Swal.fire({
                        title: "<?= $_GET['status'] === 'success' ? 'Berhasil!' : 'Gagal!' ?>",
                        text: "<?= htmlspecialchars($_GET['message']) ?>",
                        icon: "<?= $_GET['status'] === 'success' ? 'success' : 'error' ?>",
                    });
                </script>
            <?php endif; ?>
            <!-- Table -->
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Kelas</th>
                            <th scope="col" class="px-6 py-3">Nama Kelas</th>
                            <th scope="col" class="px-6 py-3">Jurusan</th>
                            <th scope="col" class="px-6 py-3">Nama Wali Kelas</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($kelas as $k): ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= $no++; ?></td>
                                <td class="px-6 py-4"><?= $k['kelas'] ?></td>
                                <td class="px-6 py-4"><?= $k['nama_kelas'] ?></td>
                                <td class="px-6 py-4"><?= $k['jurusan'] ?></td>
                                <td class="px-6 py-4"><?= $k['nama_wali_kelas'] ?></td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center" type="button"
                                        onclick="openUpdateForm(<?= $k['id_kelas'] ?>, '<?= $k['kelas'] ?>', '<?= $k['nama_kelas'] ?>', '<?= $k['jurusan'] ?>', '<?= $k['nama_wali_kelas'] ?>')">
                                        <svg class="w-6 h-6 mr-2 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                        </svg>
                                        Edit
                                    </button>
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="id_kelas" value="<?= $k['id_kelas'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="button" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-700 flex items-center" onclick="confirmDelete(this)">
                                            <svg class="w-6 h-6 mr-2 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z" clip-rule="evenodd" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Main modal -->
        <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modalFormLabel">
                            Tambah Pengguna
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5" method="POST">
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <input type="hidden" name="action" id="formAction" value="create">
                            <input type="hidden" name="id_kelas" id="idKelas">
                            <div class="col-span-2">
                                <label for="kelas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                                <select name="kelas" id="kelas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option selected="">Pilih Kelas</option>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label for="namaKelas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Kelas</label>
                                <input type="text" name="nama_kelas" id="namaKelas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required="">
                            </div>
                            <div class="col-span-2">
                                <label for="jurusan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jurusan</label>
                                <select name="jurusan" id="jurusan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option selected="">Pilih Jurusan</option>
                                    <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                    <option value="Akuntansi">Akuntansi</option>
                                    <option value="Manajemen Perkantoran">Manajemen Perkantoran</option>
                                    <option value="Bisnis Retail">Bisnis Retail</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label for="namaWaliKelas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Wali Kelas</label>
                                <input type="text" name="nama_wali_kelas" id="namaWaliKelas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required="">
                            </div>
                        </div>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../node_modules/flowbite/dist/flowbite.min.js"></script>
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        function openCreateForm() {
            document.getElementById('modalFormLabel').innerText = 'Tambah Kelas';
            document.getElementById('formAction').value = 'create';
            document.getElementById('idKelas').value = '';
            document.getElementById('kelas').value = '';
            document.getElementById('namaKelas').value = '';
            document.getElementById('jurusan').value = '';
            document.getElementById('namaWaliKelas').value = '';
        }

        function openUpdateForm(id, kelas, namaKelas, jurusan, namaWaliKelas) {
            document.getElementById('modalFormLabel').innerText = 'Edit Kelas';
            document.getElementById('formAction').value = 'update';
            document.getElementById('idKelas').value = id;
            document.getElementById('kelas').value = kelas;
            document.getElementById('namaKelas').value = namaKelas;
            document.getElementById('jurusan').value = jurusan;
            document.getElementById('namaWaliKelas').value = namaWaliKelas;
        }
        <?php if ($status === 'success'): ?>
            Swal.fire({
                title: "Good job!",
                text: "<?= $message ?>",
                icon: "success",
            }).then(() => window.location = 'kelas.php');
        <?php elseif ($status === 'error'): ?>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "<?= $message ?>",
            }).then(() => window.location = 'kelas.php');
        <?php endif; ?>
        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const match = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchTerm));
                row.style.display = match ? '' : 'none';
            });
        });

        document.getElementById('filter').addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const jurusan = row.cells[2].textContent;
                row.style.display = !filterValue || jurusan === filterValue ? '' : 'none';
            });
        });

        function confirmDelete(button) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = button.closest('form');
                    if (form) {
                        form.submit();
                    }
                }
            });
        }
    </script>

</body>

</html>