<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$username = $_SESSION['username'];

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = date("Y-m-d");
    $gambar = "";

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $namaFile = basename($_FILES['gambar']['name']);
        $targetFile = $uploadDir . time() . '_' . $namaFile;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
                $gambar = $targetFile;
            } else {
                $error = "Gagal mengunggah gambar.";
            }
        } else {
            $error = "Format gambar tidak didukung.";
        }
    }

    if (empty($error)) {
        $query = "INSERT INTO pengumuman (judul, isi, tanggal_dibuat, gambar) 
                  VALUES ('$judul', '$isi', '$tanggal', '$gambar')";
        if (mysqli_query($conn, $query)) {
            $success = "Pengumuman berhasil dikirim.";
        } else {
            $error = "Gagal menyimpan pengumuman.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Pengumuman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 text-gray-800 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-yellow-100 border-b border-yellow-300 sticky top-0 z-50 shadow-sm">
    <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center gap-2">
            <img src="Asset/logo1.png" alt="Logo" class="w-10 h-10">
            <span class="text-lg font-semibold text-yellow-900">DompetKu</span>
        </div>

        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">
                Halo, <strong><?= htmlspecialchars($username) ?></strong>
            </span>
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

<!-- Main Content -->
<main class="flex-grow px-4 py-8">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-md">
        <h1 class="text-xl font-bold mb-4 text-yellow-700">📣 Kirim Pengumuman Baru</h1>

        <?php if ($success): ?>
            <div class="bg-green-100 text-green-800 px-4 py-2 mb-4 rounded"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="bg-red-100 text-red-800 px-4 py-2 mb-4 rounded"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Judul Pengumuman</label>
                <input type="text" name="judul" required class="w-full mt-1 p-2 border border-yellow-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Isi Pengumuman</label>
                <textarea name="isi" rows="5" required class="w-full mt-1 p-2 border border-yellow-300 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Upload Gambar (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" class="mt-1 text-sm">
            </div>
            <div class="text-right">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded transition">
                    Kirim Pengumuman
                </button>
            </div>
        </form>
    </div>
</main>

<!-- Footer -->
<footer class="bg-yellow-100 mt-10">
    <div class="text-center text-xs text-yellow-800 py-4">
        © 2025 DompetKu. All rights reserved.
    </div>
</footer>

<script>
    lucide.createIcons();
</script>
</body>
</html>
