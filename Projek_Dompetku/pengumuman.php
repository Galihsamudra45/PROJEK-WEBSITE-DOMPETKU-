<?php 
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$sql_pengumuman = "SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC";
$result_pengumuman = mysqli_query($conn, $sql_pengumuman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengumuman - DompetKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 text-gray-800 flex flex-col min-h-screen">

<!-- Header -->
<header class="bg-yellow-100 dark:bg-gray-800 border-b border-yellow-300 dark:border-yellow-600 sticky top-0 z-50">
  <div class="flex items-center justify-between px-4 py-3 md:px-8">
    <div class="flex items-center gap-2">
      <img src="Asset/logo1.png" alt="Logo" class="w-10 h-10">
      <span class="text-lg font-semibold text-yellow-900 dark:text-white">DompetKu</span>
    </div>
    
    <div class="flex items-center gap-4">
      <span class="text-sm text-gray-600 dark:text-gray-300">
        Halo, <strong><?= htmlspecialchars($username) ?></strong>
      </span>

      <!-- Toggle Menu -->
      <div class="relative">
        <input type="checkbox" id="toggleMenu" class="hidden peer">
        <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-md flex items-center text-sm">
          <i data-lucide="menu" class="w-4 h-4 mr-1"></i> Menu
        </label>
        <div class="hidden peer-checked:flex flex-col absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-yellow-300 dark:border-yellow-600 rounded-md shadow-lg py-2 z-50 overflow-hidden">
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

<!-- Main -->
<main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 py-10 w-full">
  <h1 class="text-2xl font-bold text-yellow-700 mb-6">📢 Daftar Pengumuman</h1>

  <?php if (mysqli_num_rows($result_pengumuman) > 0): ?>
    <div class="space-y-6">
      <?php while ($row = mysqli_fetch_assoc($result_pengumuman)): ?>
        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-yellow-400">
          <h2 class="text-lg font-bold text-yellow-800"><?= htmlspecialchars($row['judul']) ?></h2>
          <p class="text-sm text-gray-500 mb-2">Dibuat pada: <?= date('d M Y', strtotime($row['tanggal_dibuat'])) ?></p>
          <?php if (!empty($row['gambar']) && file_exists($row['gambar'])): ?>
              <img src="<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar Pengumuman" class="w-full max-w-md mb-4 rounded-lg shadow">
          <?php endif; ?>
          <p class="text-gray-700 whitespace-pre-line"><?= nl2br(htmlspecialchars($row['isi'])) ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="text-center text-gray-600">Belum ada pengumuman saat ini.</p>
  <?php endif; ?>
</main>

<!-- Footer -->
<footer class="bg-yellow-100">
  <div class="max-w-4xl mx-auto px-4 py-4 text-center text-xs text-yellow-800">
    © 2025 DompetKu. All rights reserved.
  </div>
</footer>

<script>
  lucide.createIcons();
</script>
</body>
</html>
