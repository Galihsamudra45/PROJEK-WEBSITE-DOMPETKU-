<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $jenis = $_POST['jenis'];
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $jumlah = (int) $_POST['jumlah'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $query = "INSERT INTO transaksi (user_id, tanggal, waktu, jenis, kategori, jumlah, keterangan)
              VALUES ($user_id, '$tanggal', '$waktu', '$jenis', '$kategori', $jumlah, '$keterangan')";

    if (mysqli_query($conn, $query)) {
        $pesan = "Transaksi berhasil ditambahkan!";
    } else {
        $pesan = "Gagal menambahkan transaksi: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Transaksi - DompetKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 text-gray-800 min-h-screen flex flex-col">
  <!-- Header -->
  <header class="bg-yellow-100 border-b border-yellow-300 dark:bg-gray-800 dark:border-yellow-600 shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="Asset/logo1.png" alt="DompetKu Logo" class="w-10 h-10">
        <span class="text-lg font-semibold text-yellow-900 dark:text-white">DompetKu</span>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-700 dark:text-gray-200 hidden sm:inline">
          Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        </span>
        <!-- Menu Dropdown -->
        <div class="relative">
          <input type="checkbox" id="toggleMenu" class="hidden peer">
          <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm font-medium flex items-center gap-1">
            <i data-lucide="menu" class="w-4 h-4"></i><span>Menu</span>
          </label>
          <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 border border-yellow-300 dark:border-yellow-600 rounded-lg shadow-lg hidden peer-checked:flex flex-col z-50">
            <a href="dashboard_user.php" class="px-4 py-2 text-sm hover:bg-green-100 dark:hover:bg-yellow-600 text-gray-700 dark:text-white flex items-center gap-2">
              <i data-lucide="home" class="w-4 h-4"></i> Home
            </a>
            <a href="logout.php" class="px-4 py-2 text-sm hover:bg-red-100 dark:hover:bg-gray-600 text-gray-700 dark:text-white flex items-center gap-2">
              <i data-lucide="log-out" class="w-4 h-4"></i> Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="flex-grow">
    <div class="max-w-xl mx-auto px-4 py-10">
      <h1 class="text-2xl font-bold text-yellow-700 mb-6">Tambah Transaksi</h1>

      <?php if (!empty($pesan)): ?>
        <div class="bg-green-100 text-green-700 border border-green-300 rounded-md p-4 mb-6">
          <?= $pesan ?>
        </div>
      <?php endif; ?>

      <form method="POST" class="bg-white p-6 rounded-xl shadow border border-yellow-200 space-y-4">
        <div>
          <label class="block text-sm font-medium">Tanggal</label>
          <input type="date" name="tanggal" class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
        </div>
        <div>
          <label class="block text-sm font-medium">Waktu</label>
          <input type="time" name="waktu" class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
        </div>
        <div>
          <label class="block text-sm font-medium">Jenis Transaksi</label>
          <select name="jenis" class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
            <option value="pemasukan">Pemasukan</option>
            <option value="pengeluaran">Pengeluaran</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium">Kategori</label>
          <input type="text" name="kategori" placeholder="Contoh: Gaji, Makan, Transport" class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
        </div>
        <div>
          <label class="block text-sm font-medium">Jumlah (Rp)</label>
          <input type="number" name="jumlah" min="1" class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
        </div>
        <div>
          <label class="block text-sm font-medium">Keterangan</label>
          <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..." class="mt-1 w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
        </div>
        <div class="pt-4">
          <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-6 py-2 rounded shadow transition">Simpan Transaksi</button>
        </div>
      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-yellow-100 mt-10">
    <div class="max-w-7xl mx-auto px-6 py-5 text-center text-sm text-yellow-800">
      &copy; 2025 <span class="font-semibold">DompetKu</span>. All rights reserved.
    </div>
  </footer>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
