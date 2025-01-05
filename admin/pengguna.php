<?php
require_once '../controller/check.php';
checkAccess('admin');
include '../controller/admin.php';

$action = $_POST['action'] ?? null;
$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $role = $_POST['role'];
        $id_kelas = $_POST['id_kelas'] ?? 'NULL';

        if (createUser($username, $password, $email, $full_name, $role, $id_kelas)) {
            $status = 'success';
            $message = 'Data berhasil ditambahkan!';
        } else {
            $status = 'error';
            $message = 'Gagal menambahkan data.';
        }
    } elseif ($action === 'update') {
        $id_user = $_POST['id_user'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $full_name = $_POST['full_name'];
        $role = $_POST['role'];
        $id_kelas = $_POST['id_kelas'] ?? 'NULL';

        if (updateUser($id_user, $username, $password, $email, $full_name, $role, $id_kelas)) {
            $status = 'success';
            $message = 'Data berhasil diperbarui!';
        } else {
            $status = 'error';
            $message = 'Gagal memperbarui data.';
        }
    } elseif ($action === 'delete') {
        $id_user = $_POST['id_user'];
        if (deleteUser($id_user)) {
            $status = 'success';
            $message = 'Data berhasil dihapus!';
        } else {
            $status = 'error';
            $message = 'Gagal menghapus data.';
        }
    }
}

$users = readUsers();
$kelasList = readKelas();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pengguna</title>
    <link rel="stylesheet" href="../src/css/output.css">
    <link rel="stylesheet" href="../node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<body class="">
    <?php include '../components/sidebar_admin.php'; ?>
    <div class="p-4 pt-8 sm:ml-64">
        <div class="container mx-auto mt-10 px-4 sm:px-6 lg:px-8">
            <!-- Search and Filter Section -->
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
                        class="w-full sm:w-auto px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-none transition ease-in-out duration-150 hover:border-blue-500">
                        <option value="" class="text-gray-500">Filter Role</option>
                        <option value="Admin" class="text-gray-800">Admin</option>
                        <option value="Resepsionis" class="text-gray-800">Resepsionis</option>
                        <option value="Pengurus kelas" class="text-gray-800">Pengurus Kelas</option>
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
            <!-- Table Section -->
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Username</th>
                            <th scope="col" class="px-6 py-3">Email</th>
                            <th scope="col" class="px-6 py-3">Nama Lengkap</th>
                            <th scope="col" class="px-6 py-3">Role</th>
                            <th scope="col" class="px-6 py-3">Kelas</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($users as $user): ?>
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white"><?= $no++; ?></td>
                                <td class="px-6 py-4"><?= $user['username']; ?></td>
                                <td class="px-6 py-4"><?= $user['email']; ?></td>
                                <td class="px-6 py-4"><?= $user['full_name']; ?></td>
                                <td class="px-6 py-4"><?= ucfirst($user['role']); ?></td>
                                <td class="px-6 py-4"><?= $user['nama_kelas'] ?? '-'; ?></td>
                                <td class="px-6 py-4 flex space-x-2">
                                    <!-- Tombol Edit -->
                                    <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center"
                                        type="button"
                                        onclick="openEditForm(<?= $user['id_user'] ?>, '<?= $user['username'] ?>', '<?= $user['email'] ?>', '<?= $user['full_name'] ?>', '<?= $user['role'] ?>', <?= $user['id_kelas'] ?? 'null' ?>)">
                                        <svg class="w-6 h-6 mr-2 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                        </svg>
                                        Edit
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">
                                        <button type="button" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-700 flex items-center" onclick="confirmDelete(<?= $user['id_user'] ?>)">
                                            <svg class=" w-6 h-6 mr-2 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
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
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Header Modal -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="userModalLabel">Tambah User</h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <!-- Body Modal -->
                    <form class="p-4 md:p-5" method="POST">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id_user" id="idUser">
                        <div class="grid gap-4 mb-4">
                            <div>
                                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                                <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                            </div>
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                                <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            </div>
                            <div class="col-span-2">
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                            </div>
                            <div>
                                <label for="fullName" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                                <input type="text" name="full_name" id="fullName" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                            </div>
                            <div>
                                <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Role</label>
                                <select name="role" id="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500" required>
                                    <option value="admin">Admin</option>
                                    <option value="resepsionis">Resepsionis</option>
                                    <option value="pengurus_kelas">Pengurus Kelas</option>
                                </select>
                            </div>
                            <div>
                                <label for="idKelas" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kelas</label>
                                <select name="id_kelas" id="idKelas" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500">
                                    <option value="">- Tidak ada -</option>
                                    <?php foreach ($kelasList as $kelas): ?>
                                        <option value="<?= $kelas['id_kelas'] ?>"><?= $kelas['nama_kelas'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../node_modules/flowbite/dist/flowbite.min.js"></script>
    <script src="../node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script>
        <?php if ($status === 'success'): ?>
            Swal.fire({
                title: "Good job!",
                text: "<?= $message ?>",
                icon: "success"
            }).then(() => window.location = 'pengguna.php');
        <?php elseif ($status === 'error'): ?>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "<?= $message ?>"
            }).then(() => window.location = 'pengguna.php');
        <?php endif; ?>

        function openCreateForm() {
            document.getElementById('userModalLabel').innerText = "Tambah User";
            document.getElementById('formAction').value = "create";
            document.getElementById('idUser').value = "";
            document.getElementById('username').value = "";
            document.getElementById('password').value = "";
            document.getElementById('email').value = "";
            document.getElementById('fullName').value = "";
            document.getElementById('role').value = "admin";
            document.getElementById('idKelas').value = "";
        }

        function openEditForm(id, username, email, fullName, role, idKelas) {
            document.getElementById('userModalLabel').innerText = "Edit User";
            document.getElementById('formAction').value = "update";
            document.getElementById('idUser').value = id;
            document.getElementById('username').value = username;
            document.getElementById('password').value = "";
            document.getElementById('email').value = email;
            document.getElementById('fullName').value = fullName;
            document.getElementById('role').value = role;
            document.getElementById('idKelas').value = idKelas || "";
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'pengguna.php';

                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete';
                    form.appendChild(actionInput);

                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'id_user';
                    idInput.value = id;
                    form.appendChild(idInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

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
                const role = row.cells[4].textContent;
                row.style.display = !filterValue || role === filterValue ? '' : 'none';
            });
        });
    </script>

</body>

</html>