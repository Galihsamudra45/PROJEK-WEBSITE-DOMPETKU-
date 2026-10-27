<?php
session_start();
include 'koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password_raw = $_POST['password'];
    $role = $_POST['role'];

    if (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (strlen($password_raw) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($role !== 'user' && $role !== 'admin') {
        $error = 'Role tidak valid.';
    } else {
        // Gunakan prepared statement untuk menghindari SQL Injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($user['status_akun'] !== 'aktif') {
                $error = 'Akun Anda tidak aktif.';
            } elseif (password_verify($password_raw, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: dashboard_admin.php');
                } else {
                    header('Location: dashboard_user.php');
                }
                exit;
            } else {
                $error = 'Password salah.';
            }
        } else {
            $error = 'Username atau role tidak ditemukan.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dompetku - Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-yellow-50">

  <div class="w-full max-w-5xl mx-auto border rounded-3xl overflow-hidden shadow-lg grid grid-cols-1 md:grid-cols-2">

    <!-- Bagian Kiri (Ilustrasi) -->
    <div class="bg-yellow-400 flex items-center justify-center p-6">
      <img src="Asset/Gambar5.png" alt="Ilustrasi" class="w-96 max-w-full h-auto object-contain" />
    </div>

    <!-- Bagian Kanan (Form Login) -->
    <div class="bg-white p-10">

      <!-- Logo dan Tulisan -->
      <div class="mb-4 flex items-center gap-2">
        <img src="Asset/logo1.png" alt="Logo Aplikasi" class="h-8" />
        <span class="font-semibold text-lg text-gray-700">Dompetku</span>
      </div>

      <!-- Motivasi -->
      <p class="text-sm text-gray-500 mb-6">
        <span class="text-lg font-semibold text-gray-800">Login</span><br />
        <span class="text-gray-400">Menunggu besok? Bisa jadi kamu takkan pernah mulai.</span>
      </p>

      <!-- Pesan Error -->
      <?php if ($error): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <!-- Form Login -->
      <form action="" method="POST" class="space-y-4" novalidate>
        <div>
          <label for="username" class="block text-sm text-gray-600 mb-1">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            required
            class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400"
            placeholder="Masukkan Username"
            value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
          />
        </div>

        <div>
          <label for="password" class="block text-sm text-gray-600 mb-1">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            required
            class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400"
            placeholder="Masukkan Password"
          />
        </div>

        <div>
          <label for="role" class="block text-sm text-gray-600 mb-1">Masuk Sebagai</label>
          <select
            id="role"
            name="role"
            required
            class="w-full px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-yellow-400"
          >
            <option value="">-- Pilih Role --</option>
            <option value="user" <?= (isset($_POST['role']) && $_POST['role'] === 'user') ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>

        <button
          type="submit"
          class="w-full bg-yellow-500 text-white py-2 rounded-full hover:bg-yellow-600 transition"
        >
          Login
        </button>
      </form>

      <!-- Link ke Registrasi -->
      <p class="mt-6 text-center text-gray-600">
        Belum punya akun?
        <a href="registrasi.php" class="text-yellow-600 hover:underline font-medium">Daftar</a>
      </p>
    </div>
  </div>

</body>
</html>