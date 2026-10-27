<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$status_pengajuan = null;

// Cek pengajuan terakhir
$cek = mysqli_query($conn, "SELECT * FROM pengajuan_hapus WHERE user_id = $user_id ORDER BY tanggal_pengajuan DESC LIMIT 1");
if (mysqli_num_rows($cek) > 0) {
    $data_pengajuan = mysqli_fetch_assoc($cek);
    $status_pengajuan = $data_pengajuan['status'];
}

// Handle form pengajuan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);

    if (!$status_pengajuan || $status_pengajuan !== 'menunggu') {
        $query = "INSERT INTO pengajuan_hapus (user_id, alasan, status) VALUES ($user_id, '$alasan', 'menunggu')";
        if (mysqli_query($conn, $query)) {
            $status_pengajuan = 'menunggu';
            $success = "Pengajuan berhasil dikirim. Mohon tunggu verifikasi admin.";
        } else {
            $error = "Gagal mengirim pengajuan. Coba lagi.";
        }
    } else {
        $error = "Anda sudah mengajukan permintaan. Silakan tunggu konfirmasi admin.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nonaktifkan Akun - DompetKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-yellow-50 text-gray-800 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-yellow-100 dark:bg-gray-800 border-b border-yellow-300 dark:border-yellow-600 sticky top-0 z-50">
    <div class="flex items-center justify-between px-4 py-3 md:px-8">
        <div class="flex items-center gap-2">
            <img src="Asset/logo1.png" alt="Logo" class="w-10 h-10">
            <span class="text-lg font-semibold text-yellow-900 dark:text-white">DompetKu</span>
        </div>

        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600 dark:text-gray-300">
                Halo, <strong><?= htmlspecialchars($username) ?></strong>
            </span>

            <!-- Toggle Menu -->
            <div class="relative">
                <input type="checkbox" id="toggleMenu" class="hidden peer">
                <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-md flex items-center text-sm">
                    <i data-lucide="menu" class="w-4 h-4 mr-1"></i> Menu
                </label>
                <div class="hidden peer-checked:flex flex-col absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-yellow-300 dark:border-yellow-600 rounded-md shadow-lg py-2 z-50">
                    <a href="dashboard_user.php" class="px-4 py-2 hover:bg-green-100 dark:hover:bg-yellow-600 text-sm text-gray-800 dark:text-white flex items-center gap-2">
                        <i data-lucide="home" class="w-4 h-4"></i> Home
                    </a>
                    <a href="logout.php" class="px-4 py-2 hover:bg-red-200 dark:hover:bg-gray-600 text-sm text-gray-800 dark:text-white flex items-center gap-2">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="flex-1 max-w-xl w-full mx-auto px-4 py-10">
    <h2 class="text-2xl font-semibold mb-6 text-yellow-700 text-center">Pengajuan Nonaktifkan Akun</h2>

    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $success ?>
        </div>
    <?php elseif (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if ($status_pengajuan === 'menunggu'): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            Anda telah mengajukan permintaan. Status saat ini: <strong>Menunggu Verifikasi Admin</strong>.
        </div>
    <?php elseif ($status_pengajuan === 'disetujui'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            Pengajuan Anda telah <strong>disetujui</strong> oleh admin.
        </div>
    <?php elseif ($status_pengajuan === 'ditolak'): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            Pengajuan Anda <strong>ditolak</strong>. Anda dapat mengajukan kembali jika diperlukan.
        </div>
    <?php else: ?>
        <form method="POST" class="bg-white border border-yellow-300 p-6 rounded shadow space-y-4">
            <div>
                <label class="block font-medium mb-1">Nama Pengguna:</label>
                <input type="text" value="<?= htmlspecialchars($username) ?>" disabled class="w-full border rounded px-3 py-2 bg-gray-100">
            </div>
            <div>
                <label class="block font-medium mb-1">Alasan Nonaktifkan Akun:</label>
                <textarea name="alasan" required rows="4" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Tuliskan alasan Anda..."></textarea>
            </div>
            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-2 rounded transition">
                Kirim Pengajuan
            </button>
        </form>
    <?php endif; ?>
</main>

<!-- Footer -->
<footer class="bg-yellow-100 mt-10">
    <div class="text-center py-4 text-sm text-yellow-800">
        © 2025 DompetKu. All rights reserved.
    </div>
</footer>

<!-- Ikon -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>
    lucide.createIcons();
</script>

</body>
</html>
