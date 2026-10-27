<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$username = $_SESSION['username'];
$message = '';
$messageType = '';

// Proses penghapusan akun
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_akun'])) {
    $user_id = (int)$_POST['user_id'];
    $id_pengajuan = (int)$_POST['id_pengajuan'];
    
    // Validasi input
    if ($user_id <= 0 || $id_pengajuan <= 0) {
        $message = "Data tidak valid!";
        $messageType = "error";
    } else {
        // Mulai transaksi database
        mysqli_autocommit($conn, FALSE);
        
        try {
            // 1. Hapus semua transaksi user
            $query_transaksi = "DELETE FROM transaksi WHERE user_id = ?";
            $stmt_transaksi = mysqli_prepare($conn, $query_transaksi);
            mysqli_stmt_bind_param($stmt_transaksi, "i", $user_id);
            
            if (!mysqli_stmt_execute($stmt_transaksi)) {
                throw new Exception("Gagal menghapus data transaksi: " . mysqli_error($conn));
            }
            
            // 2. Hapus semua pengajuan hapus akun dari user ini
            $query_pengajuan = "DELETE FROM pengajuan_hapus WHERE user_id = ?";
            $stmt_pengajuan = mysqli_prepare($conn, $query_pengajuan);
            mysqli_stmt_bind_param($stmt_pengajuan, "i", $user_id);
            
            if (!mysqli_stmt_execute($stmt_pengajuan)) {
                throw new Exception("Gagal menghapus data pengajuan: " . mysqli_error($conn));
            }
            
            // 3. Hapus akun user
            $query_user = "DELETE FROM users WHERE id = ?";
            $stmt_user = mysqli_prepare($conn, $query_user);
            mysqli_stmt_bind_param($stmt_user, "i", $user_id);
            
            if (!mysqli_stmt_execute($stmt_user)) {
                throw new Exception("Gagal menghapus akun user: " . mysqli_error($conn));
            }
            
            // Cek apakah user benar-benar terhapus
            if (mysqli_stmt_affected_rows($stmt_user) === 0) {
                throw new Exception("User tidak ditemukan atau sudah terhapus!");
            }
            
            // Commit transaksi jika semua berhasil
            mysqli_commit($conn);
            
            // Tutup prepared statements
            mysqli_stmt_close($stmt_transaksi);
            mysqli_stmt_close($stmt_pengajuan);
            mysqli_stmt_close($stmt_user);
            
            $message = "Akun user dan semua data terkait berhasil dihapus permanen!";
            $messageType = "success";
            
        } catch (Exception $e) {
            // Rollback jika ada error
            mysqli_rollback($conn);
            $message = "Gagal menghapus akun: " . $e->getMessage();
            $messageType = "error";
        }
        
        // Kembalikan autocommit ke TRUE
        mysqli_autocommit($conn, TRUE);
    }
}

$query = mysqli_query($conn, "SELECT ph.id AS id_pengajuan, u.id AS user_id, u.nama_lengkap, u.email, u.username, ph.alasan, ph.tanggal_pengajuan
                              FROM pengajuan_hapus ph
                              JOIN users u ON ph.user_id = u.id
                              WHERE ph.status = 'menunggu'");
$dataTersedia = mysqli_num_rows($query) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Akun Pengguna</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 text-gray-800 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-yellow-100 border-b border-yellow-300 sticky top-0 z-50">
        <div class="flex items-center justify-between px-4 py-3 md:px-8">
            <div class="flex items-center gap-2">
                <img src="Asset/logo1.png" alt="Logo" class="w-10 h-10">
                <span class="text-lg font-semibold text-yellow-900">DompetKu</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Halo, <strong><?= htmlspecialchars($username) ?></strong></span>
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
    <main class="flex-1 container mx-auto px-4 py-6">
        <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <i data-lucide="trash-2" class="w-6 h-6"></i> Pengajuan Hapus Akun
        </h2>

        <!-- Pesan Notifikasi -->
        <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-100 border border-green-300 text-green-800' : 'bg-red-100 border border-red-300 text-red-800' ?>">
            <div class="flex items-center gap-2">
                <i data-lucide="<?= $messageType === 'success' ? 'check-circle' : 'alert-circle' ?>" class="w-5 h-5"></i>
                <span><?= htmlspecialchars($message) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-red-200 text-gray-900">
                    <tr>
                        <th class="px-4 py-3">Nama Lengkap</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Username</th>
                        <th class="px-4 py-3">Alasan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataTersedia): ?>
                        <?php while ($row = mysqli_fetch_assoc($query)) : ?>
                        <tr class="border-t hover:bg-yellow-50">
                            <td class="px-4 py-3"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['username']) ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['alasan']) ?></td>
                            <td class="px-4 py-3"><?= date('d M Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" onsubmit="return confirm('PERINGATAN: Tindakan ini akan menghapus SEMUA data user termasuk:\n- Akun pengguna\n- Semua transaksi\n- Semua pengajuan\n\nData yang sudah dihapus TIDAK DAPAT dikembalikan!\n\nYakin ingin melanjutkan?');">
                                    <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                    <input type="hidden" name="id_pengajuan" value="<?= $row['id_pengajuan'] ?>">
                                    <input type="hidden" name="hapus_akun" value="1">
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 flex items-center gap-1 mx-auto">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i> Hapus Permanen
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center px-4 py-6 text-gray-500">
                                Belum ada pengajuan penghapusan akun.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-yellow-100 text-center text-xs py-4 text-yellow-800 mt-10">
        © 2025 DompetKu. All rights reserved.
    </footer>

    <script>lucide.createIcons();</script>
</body>
</html>