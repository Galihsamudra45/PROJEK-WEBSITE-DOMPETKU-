<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$username = $_SESSION['username'];
$userQuery = mysqli_query($conn, "SELECT id, nama_lengkap, email, username, tanggal_daftar FROM users WHERE role = 'user' ORDER BY tanggal_daftar DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 text-gray-800">
<div class="flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-yellow-100 border-b border-yellow-300 sticky top-0 z-50">
        <div class="flex items-center justify-between px-4 py-3 md:px-8">
            <div class="flex items-center gap-2">
                <img src="Asset/logo1.png" alt="Logo" class="w-10 h-10">
                <span class="text-lg font-semibold text-yellow-900">DompetKu</span>
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">
                    Halo, <strong><?= htmlspecialchars($username) ?></strong>
                </span>

                <!-- Toggle Menu -->
                <div class="relative">
                    <input type="checkbox" id="toggleMenu" class="hidden peer">
                    <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-md flex items-center text-sm">
                        <i data-lucide="menu" class="w-4 h-4 mr-1"></i> Menu
                    </label>
                    <div class="hidden peer-checked:flex flex-col absolute right-0 mt-2 w-48 bg-white border border-yellow-300 rounded-md shadow-lg py-2 z-50">
                        <a href="dashboard_admin.php" class="px-4 py-2 hover:bg-green-100 text-sm text-green-800 flex items-center gap-2">
                            <i data-lucide="home" class="w-4 h-4"></i> Home
                        </a>
                        <a href="logout.php" class="px-4 py-2 hover:bg-red-200 text-sm text-gray-800 flex items-center gap-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="p-4 md:p-6 max-w-6xl mx-auto w-full flex-1">
        <h1 class="text-xl md:text-2xl font-bold mb-6 flex items-center gap-2">
            <i data-lucide="users" class="w-6 h-6"></i> Daftar Pengguna
        </h1>

        <div class="overflow-x-auto bg-white rounded-xl shadow">
            <table class="min-w-full text-sm text-left whitespace-nowrap">
                <thead class="bg-yellow-200 text-gray-800">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Nama Lengkap</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Username</th>
                        <th class="px-4 py-2">Tanggal Daftar</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($user = mysqli_fetch_assoc($userQuery)) : ?>
                        <tr class="border-t hover:bg-yellow-50">
                            <td class="px-4 py-2"><?= $no++ ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['nama_lengkap']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($user['username']) ?></td>
                            <td class="px-4 py-2"><?= date('d M Y', strtotime($user['tanggal_daftar'])) ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:underline flex items-center gap-1">
                                    <i data-lucide="edit" class="w-4 h-4"></i> Edit
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-yellow-100 mt-10">
        <div class="text-center text-xs text-yellow-800 py-4">
            © 2025 DompetKu. All rights reserved.
        </div>
    </footer>

</div>

<script>lucide.createIcons();</script>
</body>
</html>
