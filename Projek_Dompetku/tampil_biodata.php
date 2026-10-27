<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) === 0) {
    header("Location: isi_biodata.php");
    exit;
}

$data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $pekerjaan = mysqli_real_escape_string($conn, $_POST['pekerjaan']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);

    $sql_update = "UPDATE users SET 
        nama_lengkap='$nama_lengkap',
        pekerjaan='$pekerjaan',
        email='$email',
        tempat_lahir='$tempat_lahir',
        tanggal_lahir='$tanggal_lahir'
        WHERE id=$user_id";

    if (mysqli_query($conn, $sql_update)) {
        header("Location: tampil_biodata.php");
        exit;
    } else {
        $error = "Gagal mengupdate biodata.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Biodata Saya - DompetKu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 min-h-screen text-gray-700">

<!-- Header -->
<header class="flex items-center justify-between px-4 py-3 md:px-8 bg-yellow-100 dark:bg-gray-800 border-b border-yellow-300 dark:border-yellow-600 sticky top-0 z-50">
    <div class="flex items-center space-x-2">
        <img src="Asset/logo1.png" alt="Logo" class="w-10 h-10">
        <span class="text-lg font-semibold text-yellow-900 dark:text-white">DompetKu</span>
    </div>

    <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600 dark:text-gray-300">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>

        <!-- Toggle Menu -->
        <div class="relative">
            <input type="checkbox" id="toggleMenu" class="hidden peer">
            <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-md flex items-center space-x-1 text-sm">
                <i data-lucide="menu" class="w-4 h-4"></i>
                <span>Menu</span>
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
</header>

<!-- Main Content -->
<main class="max-w-xl mx-auto py-10 px-4 sm:px-6 bg-white mt-6 shadow rounded-lg">
    <h1 class="text-2xl font-bold text-yellow-700 mb-6 text-center">Biodata Saya</h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm"><?= $error ?></div>
    <?php endif; ?>

    <div class="space-y-4 text-sm">
        <div><strong>Nama Lengkap:</strong> <div><?= htmlspecialchars($data['nama_lengkap']) ?></div></div>
        <div><strong>Pekerjaan:</strong> <div><?= htmlspecialchars($data['pekerjaan']) ?></div></div>
        <div><strong>Email:</strong> <div><?= htmlspecialchars($data['email']) ?></div></div>
        <div><strong>Tempat Lahir:</strong> <div><?= htmlspecialchars($data['tempat_lahir']) ?></div></div>
        <div><strong>Tanggal Lahir:</strong> <div><?= htmlspecialchars($data['tanggal_lahir']) ?></div></div>
    </div>

    <div class="mt-6 text-center">
        <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-md font-semibold transition">
            Edit Biodata
        </button>
    </div>
</main>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center px-4">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
        <h2 class="text-xl font-semibold text-yellow-700 mb-4 text-center">Edit Biodata</h2>
        <form method="POST" class="space-y-4">
            <?php
            $fields = [
                'nama_lengkap' => 'Nama Lengkap',
                'pekerjaan' => 'Pekerjaan',
                'email' => 'Email',
                'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir',
            ];
            foreach ($fields as $name => $label):
            ?>
                <div>
                    <label class="block text-sm font-medium mb-1"><?= $label ?></label>
                    <input
                        type="<?= $name === 'tanggal_lahir' ? 'date' : ($name === 'email' ? 'email' : 'text') ?>"
                        name="<?= $name ?>"
                        required
                        class="w-full border px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-yellow-400"
                        value="<?= htmlspecialchars($data[$name]) ?>">
                </div>
            <?php endforeach; ?>
            <div class="flex justify-between pt-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Simpan</button>
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Footer -->
<footer class="bg-yellow-100 mt-10">
    <div class="max-w-6xl mx-auto px-4 py-4 text-center text-xs text-yellow-800">
        © 2025 Dompetku. All rights reserved.
    </div>
</footer>

<script>
    lucide.createIcons();
</script>
</body>
</html>
