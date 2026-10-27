<?php 
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql_cek = "SELECT * FROM users WHERE id = $user_id";
$result_cek = mysqli_query($conn, $sql_cek);
$data = mysqli_fetch_assoc($result_cek);
$mode_edit = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $pekerjaan = mysqli_real_escape_string($conn, $_POST['pekerjaan']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);

    $sql = "UPDATE users SET 
                nama_lengkap = '$nama_lengkap', 
                pekerjaan = '$pekerjaan', 
                email = '$email', 
                tempat_lahir = '$tempat_lahir', 
                tanggal_lahir = '$tanggal_lahir' 
            WHERE id = $user_id";

    if (mysqli_query($conn, $sql)) {
        header('Location: tampil_biodata.php');
        exit;
    } else {
        $error = "Gagal menyimpan biodata: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Isi Biodata - DompetKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 min-h-screen text-gray-700">

  <!-- Header -->
  <header class="flex flex-wrap justify-between items-center px-4 sm:px-6 md:px-12 py-4 bg-yellow-100 shadow border-b border-yellow-300 sticky top-0 z-50">
    <div class="flex items-center space-x-2">
      <img src="Asset/logo1.png" alt="DompetKu Logo" class="w-10 h-10">
      <span class="text-lg font-semibold text-yellow-900">DompetKu</span>
    </div>
    <div class="flex items-center space-x-4">
      <span class="text-sm text-gray-600">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>

      <!-- Menu Toggle -->
      <div class="relative">
        <input type="checkbox" id="toggleMenu" class="hidden peer">
        <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center space-x-1">
          <i data-lucide="menu" class="w-4 h-4"></i>
          <span>Menu</span>
        </label>
        <div class="hidden peer-checked:flex flex-col absolute right-0 mt-2 w-52 bg-white border border-yellow-300 rounded-lg shadow-lg py-2 z-50">
          <a href="dashboard_user.php" class="px-4 py-2 text-sm hover:bg-green-100 flex items-center gap-2 text-gray-700">
            <i data-lucide="home" class="w-4 h-4"></i> Home
          </a>
          <a href="logout.php" class="px-4 py-2 text-sm hover:bg-red-100 flex items-center gap-2 text-gray-700">
            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-xl mx-auto mt-10 px-4 sm:px-6">
    <div class="bg-white p-6 shadow rounded-xl">
      <h1 class="text-2xl font-bold text-yellow-700 mb-6 text-center">Isi Biodata</h1>

      <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <div>
          <label class="block font-medium mb-1">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-yellow-500 focus:border-yellow-500" value="<?= $data['nama_lengkap'] ?? '' ?>">
        </div>
        <div>
          <label class="block font-medium mb-1">Pekerjaan</label>
          <input type="text" name="pekerjaan" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-yellow-500 focus:border-yellow-500" value="<?= $data['pekerjaan'] ?? '' ?>">
        </div>
        <div>
          <label class="block font-medium mb-1">Email</label>
          <input type="email" name="email" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-yellow-500 focus:border-yellow-500" value="<?= $data['email'] ?? '' ?>">
        </div>
        <div>
          <label class="block font-medium mb-1">Tempat Lahir</label>
          <input type="text" name="tempat_lahir" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-yellow-500 focus:border-yellow-500" value="<?= $data['tempat_lahir'] ?? '' ?>">
        </div>
        <div>
          <label class="block font-medium mb-1">Tanggal Lahir</label>
          <input type="date" name="tanggal_lahir" required class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-yellow-500 focus:border-yellow-500" value="<?= $data['tanggal_lahir'] ?? '' ?>">
        </div>
        <div class="pt-4">
          <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-semibold transition">
            Simpan Biodata
          </button>
        </div>
      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-yellow-100 mt-10">
    <div class="max-w-6xl mx-auto px-4 py-5 text-center text-xs text-yellow-800">
      © 2025 DompetKu. All rights reserved.
    </div>
  </footer>

  <script>lucide.createIcons();</script>
</body>
</html>
