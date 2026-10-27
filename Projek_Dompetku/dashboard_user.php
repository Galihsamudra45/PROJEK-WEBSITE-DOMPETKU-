<?php 
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Tangani hapus - PINDAH KE ATAS SEBELUM QUERY DATA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_id'])) {
    $id = intval($_POST['hapus_id']);
    $delete_result = mysqli_query($conn, "DELETE FROM transaksi WHERE id = $id AND user_id = $user_id");
    
    if ($delete_result) {
        // Redirect ke halaman yang sama untuk mencegah re-submission
        $redirect_url = $_SERVER['PHP_SELF'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $redirect_url .= '?' . $_SERVER['QUERY_STRING'];
        }
        header("Location: $redirect_url");
        exit;
    }
}

// Tangani update - JUGA PINDAH KE ATAS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = intval($_POST['edit_id']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $jumlah = floatval($_POST['jumlah']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $update_result = mysqli_query($conn, "UPDATE transaksi SET kategori='$kategori', jumlah=$jumlah, keterangan='$keterangan' WHERE id = $id AND user_id = $user_id");
    
    if ($update_result) {
        // Redirect ke halaman yang sama untuk mencegah re-submission
        $redirect_url = $_SERVER['PHP_SELF'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $redirect_url .= '?' . $_SERVER['QUERY_STRING'];
        }
        header("Location: $redirect_url");
        exit;
    }
}

// SEKARANG HITUNG DATA SETELAH PROSES HAPUS/UPDATE
$sql_pemasukan = "SELECT SUM(jumlah) AS total FROM transaksi WHERE user_id=$user_id AND jenis='pemasukan'";
$sql_pengeluaran = "SELECT SUM(jumlah) AS total FROM transaksi WHERE user_id=$user_id AND jenis='pengeluaran'";
$pemasukan = mysqli_fetch_assoc(mysqli_query($conn, $sql_pemasukan))['total'] ?? 0;
$pengeluaran = mysqli_fetch_assoc(mysqli_query($conn, $sql_pengeluaran))['total'] ?? 0;
$saldo = $pemasukan - $pengeluaran;

$pengumuman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC LIMIT 1"));

// Data untuk grafik
$sql_grafik = "
    SELECT 
        MONTH(tanggal) AS bulan,
        SUM(CASE WHEN jenis = 'pemasukan' THEN jumlah ELSE 0 END) AS total_pemasukan,
        SUM(CASE WHEN jenis = 'pengeluaran' THEN jumlah ELSE 0 END) AS total_pengeluaran
    FROM transaksi
    WHERE user_id = $user_id
    GROUP BY MONTH(tanggal)
    ORDER BY bulan ASC
";

$result_grafik = mysqli_query($conn, $sql_grafik);
$labels = $pemasukan_bulanan = $pengeluaran_bulanan = [];
$nama_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
while ($row = mysqli_fetch_assoc($result_grafik)) {
    $labels[] = $nama_bulan[$row['bulan'] - 1];
    $pemasukan_bulanan[] = $row['total_pemasukan'];
    $pengeluaran_bulanan[] = $row['total_pengeluaran'];
}

// Filter untuk tabel transaksi
$tanggal = $_GET['tanggal'] ?? '';
$jenis = $_GET['jenis'] ?? '';
$tahun = $_GET['tahun'] ?? '';

$where = "WHERE user_id = $user_id";

if (!empty($tanggal)) {
    $where .= " AND tanggal = '" . mysqli_real_escape_string($conn, $tanggal) . "'";
}
if (!empty($jenis)) {
    $where .= " AND jenis = '" . mysqli_real_escape_string($conn, $jenis) . "'";
}
if (!empty($tahun)) {
    $where .= " AND YEAR(tanggal) = " . intval($tahun);
}

$sql_transaksi = "SELECT * FROM transaksi $where ORDER BY tanggal DESC, waktu DESC";
$result_transaksi = mysqli_query($conn, $sql_transaksi);

$edit_id = $_POST['edit_id_show'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard User - DompetKu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-yellow-50 min-h-screen text-gray-700">
<header class="flex flex-wrap items-center justify-between px-6 md:px-12 py-4 shadow bg-yellow-100 dark:bg-gray-800 sticky top-0 z-50 transition-colors duration-300 border-b border-yellow-300 dark:border-yellow-600">
  <div class="flex items-center space-x-2">
    <img src="Asset/logo1.png" alt="DompetKu Logo" class="w-10 h-10">
    <span class="text-lg font-semibold text-yellow-900 dark:text-white">DompetKu</span>
  </div>
  <div class="relative">
    <div class="flex items-center space-x-4">
      <span class="text-sm text-gray-600 dark:text-gray-300">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
      <div class="relative">
        <input type="checkbox" id="toggleMenu" class="hidden peer">
        <label for="toggleMenu" class="cursor-pointer bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center space-x-1">
          <i data-lucide="menu" class="w-4 h-4"></i>
          <span>Menu</span>
        </label>
        <div class="hidden peer-checked:flex flex-col absolute right-0 mt-2 w-56 bg-white dark:bg-gray-700 border border-yellow-300 dark:border-yellow-600 rounded-lg shadow-lg py-2 z-50">
          <a href="isi_biodata.php" class="px-4 py-2 text-sm text-green-700 dark:text-white hover:bg-green-100 dark:hover:bg-yellow-600 flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Isi Biodata
          </a>
          <a href="tampil_biodata.php" class="px-4 py-2 text-sm text-blue-700 dark:text-white hover:bg-blue-100 dark:hover:bg-yellow-600 flex items-center gap-2">
            <i data-lucide="users" class="w-4 h-4"></i> Tampilkan Biodata
          </a>
          <a href="tambah_transaksi.php" class="px-4 py-2 text-sm text-stone-900 dark:text-white hover:bg-stone-300 dark:hover:bg-yellow-600 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Transaksi
          </a>
          <a href="nonaktif_akun.php" class="px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-600 flex items-center gap-2">
            <i data-lucide="user-x" class="w-4 h-4"></i> Nonaktif Akun
          </a>
          <a href="pengumuman.php" class="px-4 py-2 text-sm text-yellow-800 dark:text-yellow-300 hover:bg-yellow-100 dark:hover:bg-yellow-600 flex items-center gap-2">
            <i data-lucide="megaphone" class="w-4 h-4"></i> Pengumuman
          </a>
          <a href="logout.php" class="px-4 py-2 text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center gap-2">
            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 md:px-6 py-6 md:py-8">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white shadow p-4 rounded-xl border-l-4 border-yellow-500">
      <h2 class="font-semibold">Saldo Saat Ini</h2>
      <p class="text-2xl font-bold text-green-600">Rp <?= number_format($saldo, 2, ',', '.') ?></p>
    </div>
    <div class="bg-white shadow p-4 rounded-xl border-l-4 border-green-500">
      <h2 class="font-semibold">Total Pemasukan</h2>
      <p class="text-xl font-bold text-green-500">Rp <?= number_format($pemasukan, 2, ',', '.') ?></p>
    </div>
    <div class="bg-white shadow p-4 rounded-xl border-l-4 border-red-500">
      <h2 class="font-semibold">Total Pengeluaran</h2>
      <p class="text-xl font-bold text-red-500">Rp <?= number_format($pengeluaran, 2, ',', '.') ?></p>
    </div>
  </div>

  <div class="bg-white shadow rounded-xl p-4 mb-6">
    <h2 class="text-lg font-semibold text-yellow-600 mb-4">Grafik Keuangan Bulanan</h2>
    <canvas id="keuanganChart" height="100"></canvas>
  </div>

  <div class="bg-white rounded-xl shadow p-4 mb-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <input type="date" name="tanggal" class="border rounded-md px-3 py-2 w-full" value="<?= $_GET['tanggal'] ?? '' ?>">
      <select name="jenis" class="border rounded-md px-3 py-2 w-full">
        <option value="">Semua Jenis</option>
        <option value="pemasukan" <?= (isset($_GET['jenis']) && $_GET['jenis'] === 'pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
        <option value="pengeluaran" <?= (isset($_GET['jenis']) && $_GET['jenis'] === 'pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
      </select>
      <input type="number" name="tahun" placeholder="Tahun" class="border rounded-md px-3 py-2 w-full" value="<?= $_GET['tahun'] ?? '' ?>">
      <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">Filter</button>
    </form>
  </div>

  <div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <h2 class="text-lg font-semibold mb-4">Riwayat Transaksi</h2>
    <table class="min-w-full table-auto border border-gray-200 text-sm">
      <thead class="bg-yellow-100 text-yellow-800">
        <tr>
          <th class="px-4 py-2 border">Tanggal</th>
          <th class="px-4 py-2 border">Waktu</th>
          <th class="px-4 py-2 border">Jenis</th>
          <th class="px-4 py-2 border">Kategori</th>
          <th class="px-4 py-2 border">Jumlah</th>
          <th class="px-4 py-2 border">Keterangan</th>
          <th class="px-4 py-2 border">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
            <tr class="hover:bg-yellow-50 bg-yellow-50">
              <td class="px-4 py-2 border"><?= $row['tanggal'] ?></td>
              <td class="px-4 py-2 border"><?= $row['waktu'] ?></td>
              <td class="px-4 py-2 border text-<?= $row['jenis'] === 'pemasukan' ? 'green' : 'red' ?>-600 font-medium"><?= ucfirst($row['jenis']) ?></td>
              <td class="px-4 py-2 border"><?= htmlspecialchars($row['kategori']) ?></td>
              <td class="px-4 py-2 border">Rp <?= number_format($row['jumlah'], 2, ',', '.') ?></td>
              <td class="px-4 py-2 border"><?= htmlspecialchars($row['keterangan']) ?></td>
              <td class="px-4 py-2 border space-x-2">
                <?php if ($edit_id == $row['id']): ?>
                  <form method="POST" class="space-y-1">
                    <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
                    <input type="text" name="kategori" value="<?= htmlspecialchars($row['kategori']) ?>" class="border px-2 py-1 w-full rounded">
                    <input type="number" step="0.01" name="jumlah" value="<?= $row['jumlah'] ?>" class="border px-2 py-1 w-full rounded">
                    <input type="text" name="keterangan" value="<?= htmlspecialchars($row['keterangan']) ?>" class="border px-2 py-1 w-full rounded">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1 text-xs">
                      <i data-lucide="check" class="w-3 h-3"></i>
                      Simpan
                    </button>
                    <form method="POST" style="display:inline;">
                      <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded flex items-center gap-1 text-xs mt-1">
                        <i data-lucide="x" class="w-3 h-3"></i>
                        Batal
                      </button>
                    </form>
                  </form>
                <?php else: ?>
                  <div class="flex gap-1">
                    <form method="POST" style="display:inline;">
                      <input type="hidden" name="edit_id_show" value="<?= $row['id'] ?>">
                      <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs flex items-center gap-1 transition-colors">
                        <i data-lucide="edit-2" class="w-3 h-3"></i>
                        Edit
                      </button>
                    </form>
                    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')" style="display:inline;">
                      <input type="hidden" name="hapus_id" value="<?= $row['id'] ?>">
                      <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs flex items-center gap-1 transition-colors">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                        Hapus
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="px-4 py-4 text-center text-gray-500">Tidak ada transaksi ditemukan.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<footer class="bg-yellow-100 mt-10">
  <div class="max-w-6xl mx-auto px-4 py-5 text-center text-xs text-yellow-800">
    © 2025 Dompetku. All rights reserved.
  </div>
</footer>

<script>
const ctx = document.getElementById('keuanganChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($labels) ?>,
    datasets: [
      {
        label: 'Pemasukan',
        data: <?= json_encode($pemasukan_bulanan) ?>,
        backgroundColor: '#FACC15'
      },
      {
        label: 'Pengeluaran',
        data: <?= json_encode($pengeluaran_bulanan) ?>,
        backgroundColor: '#FB923C'
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'top' },
      title: { display: true, text: 'Grafik Keuangan Bulanan' }
    },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
<script>lucide.createIcons();</script>
</body>
</html>