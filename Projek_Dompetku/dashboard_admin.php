<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

// Handle edit pengumuman
if (isset($_POST['action']) && $_POST['action'] === 'edit_pengumuman') {
    $id = (int)$_POST['id'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);
    
    // Handle image upload
    $gambar_query = "";
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/pengumuman/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Check file size (max 2MB)
            if ($_FILES['gambar']['size'] <= 2 * 1024 * 1024) {
                $new_filename = 'pengumuman_' . $id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    $old_image_query = mysqli_query($conn, "SELECT gambar FROM pengumuman WHERE id = $id");
                    if ($old_image_query && mysqli_num_rows($old_image_query) > 0) {
                        $old_image = mysqli_fetch_assoc($old_image_query);
                        if ($old_image['gambar'] && file_exists($old_image['gambar'])) {
                            unlink($old_image['gambar']);
                        }
                    }
                    $gambar_query = ", gambar = '" . mysqli_real_escape_string($conn, $upload_path) . "'";
                }
            }
        }
    }
    
    // Handle image deletion
    if (isset($_POST['delete_image']) && $_POST['delete_image'] == '1') {
        $old_image_query = mysqli_query($conn, "SELECT gambar FROM pengumuman WHERE id = $id");
        if ($old_image_query && mysqli_num_rows($old_image_query) > 0) {
            $old_image = mysqli_fetch_assoc($old_image_query);
            if ($old_image['gambar'] && file_exists($old_image['gambar'])) {
                unlink($old_image['gambar']);
            }
        }
        $gambar_query = ", gambar = NULL";
    }
    
    $updateQuery = "UPDATE pengumuman SET judul = '$judul', isi = '$isi' $gambar_query WHERE id = $id";
    $result = mysqli_query($conn, $updateQuery);
    
    if ($result) {
        $_SESSION['success_message'] = "Pengumuman berhasil diperbarui!";
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui pengumuman: " . mysqli_error($conn);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete pengumuman
if (isset($_POST['action']) && $_POST['action'] === 'delete_pengumuman') {
    $id = (int)$_POST['id'];
    
    // Delete associated image first
    $image_query = mysqli_query($conn, "SELECT gambar FROM pengumuman WHERE id = $id");
    if ($image_query && mysqli_num_rows($image_query) > 0) {
        $image_data = mysqli_fetch_assoc($image_query);
        if ($image_data['gambar'] && file_exists($image_data['gambar'])) {
            unlink($image_data['gambar']);
        }
    }
    
    $deleteQuery = "DELETE FROM pengumuman WHERE id = $id";
    $result = mysqli_query($conn, $deleteQuery);
    
    if ($result) {
        $_SESSION['success_message'] = "Pengumuman berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus pengumuman: " . mysqli_error($conn);
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$queryUserCount = mysqli_query($conn, "SELECT COUNT(*) AS total_user FROM users WHERE role = 'user'");
$dataUserCount = mysqli_fetch_assoc($queryUserCount);

$grafikQuery = mysqli_query($conn, "SELECT DATE_FORMAT(tanggal_daftar, '%Y-%m') AS bulan, COUNT(*) AS jumlah FROM users WHERE role='user' GROUP BY bulan ORDER BY bulan");
$bulan = $jumlah = [];
while ($row = mysqli_fetch_assoc($grafikQuery)) {
    $bulan[] = $row['bulan'];
    $jumlah[] = $row['jumlah'];
}

$hapusQuery = mysqli_query($conn, "SELECT ph.id, u.username, u.email, ph.alasan FROM pengajuan_hapus ph JOIN users u ON ph.user_id = u.id");
$pengumumanQuery = mysqli_query($conn, "SELECT id, judul, isi, gambar, tanggal_dibuat FROM pengumuman ORDER BY tanggal_dibuat DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 text-gray-800 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-yellow-100 dark:bg-gray-800 sticky top-0 z-50 border-b border-yellow-300 dark:border-yellow-600 shadow px-4 py-4 md:px-8 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="Asset/logo1.png" alt="DompetKu Logo" class="w-10 h-10">
            <span class="text-lg font-bold text-yellow-900 dark:text-white">DompetKu</span>
        </div>
        <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-600 dark:text-gray-300">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <div class="relative">
                <input type="checkbox" id="toggleMenu" class="hidden peer">
                <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-1">
                    <i data-lucide="menu" class="w-4 h-4"></i> Menu
                </label>
                <div class="hidden peer-checked:flex flex-col absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 border border-yellow-300 dark:border-yellow-600 rounded-lg shadow-lg py-2 z-50">
                    <a href="lihat_data.php" class="px-4 py-2 text-sm text-green-700 dark:text-white hover:bg-green-100 dark:hover:bg-yellow-600 flex items-center gap-2"><i data-lucide="users" class="w-4 h-4"></i> Daftar Pengguna</a>
                    <a href="hapus_akun.php" class="px-4 py-2 text-sm text-red-700 dark:text-white hover:bg-red-100 dark:hover:bg-yellow-600 flex items-center gap-2"><i data-lucide="trash" class="w-4 h-4"></i> Hapus Akun</a>
                    <a href="kirim_pengumuman.php" class="px-4 py-2 text-sm text-blue-700 dark:text-white hover:bg-blue-100 dark:hover:bg-yellow-600 flex items-center gap-2"><i data-lucide="megaphone" class="w-4 h-4"></i> Buat Pengumuman</a>
                    <a href="logout.php" class="px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center gap-2"><i data-lucide="log-out" class="w-4 h-4"></i> Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-1 p-4 md:p-8 max-w-6xl mx-auto w-full">
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success_message']) ?></span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error_message']) ?></span>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Statistik -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Total User Card - White Background with Larger Content -->
            <div class="bg-white rounded-xl p-8 shadow-lg transform hover:scale-105 transition-transform duration-200 border border-gray-100">
                <div class="flex flex-col items-center justify-center h-full text-center">
                    <div class="bg-yellow-100 rounded-full p-6 mb-6">
                        <i data-lucide="users" class="w-16 h-16 text-yellow-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Total User Terdaftar</h2>
                    <p class="text-7xl font-bold text-yellow-600 mb-2"><?= number_format($dataUserCount['total_user']) ?></p>
                    <p class="text-lg text-gray-600 font-medium">Pengguna Aktif</p>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white rounded-xl p-6 shadow-lg">
                <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-yellow-500"></i> Pelanggan Baru per Bulan
                </h2>
                <div class="h-[300px] w-full">
                    <canvas id="grafikPelanggan"></canvas>
                </div>
            </div>
        </section>

        <!-- Pengajuan Hapus Akun -->
        <section class="mb-10">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                <i data-lucide="user-x" class="w-6 h-6 text-red-500"></i>
                Permintaan Penghapusan Akun
            </h2>
            <div class="overflow-x-auto bg-white rounded-xl shadow-lg">
                <table class="min-w-full text-sm">
                    <thead class="bg-red-200">
                        <tr>
                            <th class="py-3 px-4 text-left font-semibold">Username</th>
                            <th class="py-3 px-4 text-left font-semibold">Email</th>
                            <th class="py-3 px-4 text-left font-semibold">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($hapusQuery && mysqli_num_rows($hapusQuery) > 0): ?>
                            <?php while ($h = mysqli_fetch_assoc($hapusQuery)): ?>
                            <tr class="border-t hover:bg-red-50 transition-colors">
                                <td class="py-3 px-4 font-medium"><?= htmlspecialchars($h['username']); ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($h['email']); ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($h['alasan']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="py-4 px-4 text-center text-gray-500">Tidak ada permintaan penghapusan akun</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Pengumuman Terkini - Scrollable with Image Edit/Delete -->
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <i data-lucide="megaphone" class="w-6 h-6 text-yellow-500"></i>
                    Pengumuman Terkini
                </h2>
                <span class="text-sm text-gray-500">5 terbaru</span>
            </div>
            
            <!-- Scrollable Container -->
            <div class="bg-white rounded-lg shadow-lg p-4 max-h-96 overflow-y-auto">
                <div class="space-y-4">
                    <?php if ($pengumumanQuery && mysqli_num_rows($pengumumanQuery) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($pengumumanQuery)): ?>
                        <div class="border-l-4 border-yellow-400 pl-4 pb-4 border-b border-gray-100 last:border-b-0" id="pengumuman-<?= $p['id'] ?>">
                            <!-- View Mode -->
                            <div class="view-mode">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">
                                        <?= htmlspecialchars(date('d M Y', strtotime($p['tanggal_dibuat']))); ?>
                                    </span>
                                    <div class="flex gap-1">
                                        <button onclick="editPengumuman(<?= $p['id'] ?>)" class="p-1 text-blue-500 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                        <button onclick="deletePengumuman(<?= $p['id'] ?>)" class="p-1 text-red-500 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <h3 class="font-semibold text-yellow-700 mb-2 text-base leading-tight" id="judul-<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['judul']); ?>
                                </h3>
                                
                                <?php if (!empty($p['gambar']) && file_exists($p['gambar'])): ?>
                                    <img src="<?= htmlspecialchars($p['gambar']); ?>" alt="Gambar Pengumuman" class="w-20 h-20 object-cover mb-2 rounded-md shadow-sm float-left mr-3">
                                <?php endif; ?>
                                
                                <div class="text-gray-700 text-sm leading-relaxed" id="isi-<?= $p['id'] ?>">
                                    <?= nl2br(htmlspecialchars(strlen($p['isi']) > 150 ? substr($p['isi'], 0, 150) . '...' : $p['isi'])); ?>
                                </div>
                                <div class="clear-both"></div>
                            </div>

                            <!-- Edit Mode (Hidden by default) -->
                            <div class="edit-mode hidden">
                                <form onsubmit="savePengumuman(event, <?= $p['id'] ?>)" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                                        <input type="text" id="edit-judul-<?= $p['id'] ?>" value="<?= htmlspecialchars($p['judul']) ?>" 
                                               class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500 focus:border-transparent text-sm" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Isi</label>
                                        <textarea id="edit-isi-<?= $p['id'] ?>" rows="3" 
                                                  class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-yellow-500 focus:border-transparent text-sm" required><?= htmlspecialchars($p['isi']) ?></textarea>
                                    </div>
                                    
                                    <!-- Image Management -->
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
                                        
                                        <?php if (!empty($p['gambar']) && file_exists($p['gambar'])): ?>
                                            <div class="flex items-center gap-3 mb-2" id="current-image-<?= $p['id'] ?>">
                                                <img src="<?= htmlspecialchars($p['gambar']); ?>" alt="Current Image" class="w-16 h-16 object-cover rounded border">
                                                <div class="flex flex-col gap-1">
                                                    <button type="button" onclick="deleteCurrentImage(<?= $p['id'] ?>)" 
                                                            class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 flex items-center gap-1">
                                                        <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus Gambar
                                                    </button>
                                                    <span class="text-xs text-gray-500">Atau upload gambar baru untuk mengganti</span>
                                                </div>
                                            </div>
                                            <input type="hidden" id="delete-image-<?= $p['id'] ?>" name="delete_image" value="0">
                                        <?php endif; ?>
                                        
                                        <input type="file" id="edit-gambar-<?= $p['id'] ?>" name="gambar" accept="image/*" 
                                               class="w-full p-2 border border-gray-300 rounded-md text-sm file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100">
                                        <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, GIF. Max: 2MB</p>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm flex items-center gap-1">
                                            <i data-lucide="check" class="w-4 h-4"></i> Simpan
                                        </button>
                                        <button type="button" onclick="cancelEdit(<?= $p['id'] ?>)" class="px-3 py-1 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm flex items-center gap-1">
                                            <i data-lucide="x" class="w-4 h-4"></i> Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center text-gray-500 py-8">
                            <i data-lucide="megaphone" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                            <p>Belum ada pengumuman</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-yellow-100 border-t border-yellow-200">
        <div class="max-w-6xl mx-auto px-4 py-4 text-center text-sm text-yellow-800">
            © 2025 DompetKu. All rights reserved.
        </div>
    </footer>

    <!-- Hidden forms for actions -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete_pengumuman">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <script>
        // Chart
        const ctx = document.getElementById('grafikPelanggan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($bulan); ?>,
                datasets: [{
                    label: 'User Baru',
                    data: <?= json_encode($jumlah); ?>,
                    backgroundColor: 'rgba(251, 191, 36, 0.8)',
                    borderColor: 'rgba(251, 191, 36, 1)',
                    borderWidth: 2,
                    borderRadius: 4,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Pengumuman functions
        function editPengumuman(id) {
            const pengumuman = document.getElementById('pengumuman-' + id);
            pengumuman.querySelector('.view-mode').classList.add('hidden');
            pengumuman.querySelector('.edit-mode').classList.remove('hidden');
        }

        function cancelEdit(id) {
            const pengumuman = document.getElementById('pengumuman-' + id);
            pengumuman.querySelector('.view-mode').classList.remove('hidden');
            pengumuman.querySelector('.edit-mode').classList.add('hidden');
            
            // Reset delete image flag and show current image again
            const deleteImageInput = document.getElementById('delete-image-' + id);
            if (deleteImageInput) {
                deleteImageInput.value = '0';
            }
            
            const currentImageDiv = document.getElementById('current-image-' + id);
            if (currentImageDiv) {
                currentImageDiv.style.display = 'flex';
                const deleteButton = currentImageDiv.querySelector('button');
                if (deleteButton) {
                    deleteButton.innerHTML = '<i data-lucide="trash-2" class="w-3 h-3"></i> Hapus Gambar';
                    deleteButton.classList.remove('bg-orange-500');
                    deleteButton.classList.add('bg-red-500', 'hover:bg-red-600');
                    deleteButton.disabled = false;
                }
            }
            
            // Reset file input
            const fileInput = document.getElementById('edit-gambar-' + id);
            if (fileInput) {
                fileInput.value = '';
            }
        }

        function deleteCurrentImage(id) {
            if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                document.getElementById('delete-image-' + id).value = '1';
                
                // Hide the current image preview
                const currentImageDiv = document.getElementById('current-image-' + id);
                if (currentImageDiv) {
                    currentImageDiv.style.display = 'none';
                }
                
                // Show confirmation message
                const pengumuman = document.getElementById('pengumuman-' + id);
                const editMode = pengumuman.querySelector('.edit-mode');
                
                // Create or update confirmation message
                let confirmMsg = editMode.querySelector('.delete-confirmation');
                if (!confirmMsg) {
                    confirmMsg = document.createElement('div');
                    confirmMsg.className = 'delete-confirmation bg-orange-100 border border-orange-400 text-orange-700 px-3 py-2 rounded text-sm mb-3';
                    editMode.insertBefore(confirmMsg, editMode.querySelector('form').children[2]);
                }
                confirmMsg.innerHTML = '<i data-lucide="info" class="w-4 h-4 inline mr-1"></i> Gambar akan dihapus saat menyimpan perubahan';
            }
        }

        function savePengumuman(event, id) {
            event.preventDefault();
            const judul = document.getElementById('edit-judul-' + id).value.trim();
            const isi = document.getElementById('edit-isi-' + id).value.trim();
            
            if (!judul || !isi) {
                alert('Judul dan isi pengumuman tidak boleh kosong!');
                return;
            }
            
            const gambarFile = document.getElementById('edit-gambar-' + id).files[0];
            const deleteImage = document.getElementById('delete-image-' + id) ? 
                                document.getElementById('delete-image-' + id).value : '0';
            
            // Validate file size if file is selected
            if (gambarFile && gambarFile.size > 2 * 1024 * 1024) {
                alert('Ukuran file gambar tidak boleh lebih dari 2MB!');
                return;
            }
            
            // Create FormData for file upload
            const formData = new FormData();
            formData.append('action', 'edit_pengumuman');
            formData.append('id', id);
            formData.append('judul', judul);
            formData.append('isi', isi);
            formData.append('delete_image', deleteImage);
            
            if (gambarFile) {
                formData.append('gambar', gambarFile);
            }
            
            // Show loading state
            const submitButton = event.target.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menyimpan...';
            submitButton.disabled = true;
            
            // Submit using fetch to handle file upload
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Network response was not ok');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan pengumuman. Silakan coba lagi.');
                
                // Restore button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        }

        function deletePengumuman(id) {
            if (confirm('Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        // Initialize Lucide icons
        lucide.createIcons();
        
        // Auto-hide success/error messages after 5 seconds
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    </script>
</body>
</html>