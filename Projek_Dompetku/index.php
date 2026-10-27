<?php 
include "koneksi.php";
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DompetKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class'
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="antialiased text-yellow-900 dark:text-yellow-100 dark:bg-gray-900 transition-colors duration-300">
  <!-- Header -->
  <header class="flex flex-wrap items-center justify-between px-6 md:px-12 py-4 shadow bg-yellow-100 dark:bg-gray-800 sticky top-0 z-50 transition-colors duration-300 border-b border-yellow-300 dark:border-yellow-600">
    <div class="flex items-center space-x-2">
      <img src="Asset/logo1.png" alt="DompetKu Logo" class="w-10 h-10">
      <span class="text-lg font-semibold text-yellow-900 dark:text-white">DompetKu</span>
    </div>
    <nav class="flex space-x-4 md:space-x-6 mt-2 md:mt-0">
      <a href="#beranda" class="text-sm text-yellow-800 dark:text-yellow-100 hover:text-yellow-600 dark:hover:text-yellow-400">Beranda</a>
      <a href="#fitur" class="text-sm text-yellow-800 dark:text-yellow-100 hover:text-yellow-600 dark:hover:text-yellow-400">Fitur</a>
      <a href="#testimoni" class="text-sm text-yellow-800 dark:text-yellow-100 hover:text-yellow-600 dark:hover:text-yellow-400">Testimoni</a>
    </nav>
    <div class="flex space-x-3 mt-2 md:mt-0">
      <button id="toggle-theme" class="text-sm px-4 py-2 border rounded text-yellow-700 dark:text-white border-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-800 transition-colors">🌙</button>
      <a href="login.php" class="text-sm px-4 py-2 text-yellow-600 border border-yellow-600 rounded hover:bg-yellow-50 dark:hover:bg-yellow-800">Masuk</a>
      <a href="registrasi.php" class="text-sm px-4 py-2 text-white bg-yellow-600 rounded hover:bg-yellow-700">Daftar</a>
    </div>
  </header>

  <!-- Hero Section -->
  <section id="beranda" class="px-6 md:px-6 lg:px-12 py-16 bg-white dark:bg-gray-900 transition-colors">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 items-center gap-8">
      <div>
        <h1 class="text-3xl md:text-5xl font-bold leading-tight text-yellow-900 dark:text-yellow-100">
          Kelola Keuangan <span class="text-yellow-600">Pribadi Anda</span> dengan DompetKu
        </h1>
        <p class="text-base text-yellow-700 dark:text-yellow-300 mt-4">
          DompetKu membantu Anda mencatat pengeluaran dan pemasukan harian, membuat perencanaan anggaran, dan menganalisis kebiasaan finansial dengan mudah.
        </p>
        <button class="mt-6 px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm shadow-md transition duration-300">
          Mulai Sekarang
        </button>
      </div>
      <div class="flex justify-center md:justify-end">
        <img src="Asset/Gambar1.png" alt="Ilustrasi Keuangan Pribadi" class="w-full max-w-md md:max-w-sm">
      </div>
    </div>
  </section>

  <!-- Fitur Section -->
  <section id="fitur" class="px-6 md:px-12 py-16 bg-yellow-50 dark:bg-gray-800 transition-colors">
    <div class="text-center">
      <h2 class="text-xl text-yellow-600 font-semibold">Fitur Unggulan</h2>
      <h3 class="text-2xl font-semibold mt-2 text-yellow-900 dark:text-white">DompetKu Menawarkan Solusi Finansial</h3>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 mt-12">
      <div class="flex flex-col items-center text-center">
        <div class="bg-yellow-500 rounded-full p-4 mb-4">
          <img src="Asset/icon1.png" alt="Catat Transaksi" class="w-10 h-10">
        </div>
        <h4 class="font-bold mb-1">Catat Transaksi</h4>
        <p class="text-sm text-yellow-700 dark:text-yellow-300">Mudah mencatat setiap pemasukan dan pengeluaran secara detail.</p>
      </div>
      <div class="flex flex-col items-center text-center">
        <div class="bg-yellow-400 rounded-full p-4 mb-4">
          <img src="Asset/icon2.png" alt="Anggaran" class="w-10 h-10">
        </div>
        <h4 class="font-bold mb-1">Anggaran Bulanan</h4>
        <p class="text-sm text-yellow-700 dark:text-yellow-300">Buat anggaran bulanan dan kontrol pengeluaran Anda.</p>
      </div>
      <div class="flex flex-col items-center text-center">
        <div class="bg-yellow-500 rounded-full p-4 mb-4">
          <img src="Asset/icon3.png" alt="Grafik" class="w-10 h-10">
        </div>
        <h4 class="font-bold mb-1">Analisis Keuangan</h4>
        <p class="text-sm text-yellow-700 dark:text-yellow-300">Lihat grafik dan laporan keuangan secara visual dan interaktif.</p>
      </div>
      <div class="flex flex-col items-center text-center">
        <div class="bg-yellow-400 rounded-full p-4 mb-4">
          <img src="Asset/icon4.png" alt="Mode Gelap" class="w-10 h-10">
        </div>
        <h4 class="font-bold mb-1">Mode Gelap</h4>
        <p class="text-sm text-yellow-700 dark:text-yellow-300">Nyaman digunakan kapan saja dengan tema terang dan gelap.</p>
      </div>
    </div>
  </section>

  <!-- Testimoni Section -->
  <section id="testimoni" class="px-6 md:px-12 py-16 bg-yellow-300 dark:bg-gray-800 transition-colors">
    <div class="max-w-4xl mx-auto text-center">
      <h2 class="text-xl text-yellow-900 font-semibold">Apa Kata Mereka?</h2>
      <h3 class="text-2xl font-semibold mt-2 text-yellow-900 dark:text-white">Testimoni Pengguna DompetKu</h3>
    </div>
    <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow text-center">
        <p class="text-sm italic text-yellow-800 dark:text-yellow-200">"DompetKu membantu saya menyadari pengeluaran kecil yang sering terlupakan. Sekarang saya bisa menabung lebih konsisten."</p>
        <h4 class="mt-4 font-bold text-yellow-900 dark:text-white">Siti, Mahasiswa</h4>
      </div>
      <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow text-center">
        <p class="text-sm italic text-yellow-800 dark:text-yellow-200">"Aplikasi ini sangat mudah digunakan, fitur grafiknya membantu saya memahami alur keuangan harian saya."</p>
        <h4 class="mt-4 font-bold text-yellow-900 dark:text-white">Dwi, Freelancer</h4>
      </div>
      <div class="bg-white dark:bg-gray-900 p-6 rounded-xl shadow text-center">
        <p class="text-sm italic text-yellow-800 dark:text-yellow-200">"Fitur pencatatannya praktis banget. Bahkan ibu saya sekarang juga pakai DompetKu!"</p>
        <h4 class="mt-4 font-bold text-yellow-900 dark:text-white">Rina, Ibu Rumah Tangga</h4>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-yellow-100 dark:bg-gray-900 px-6 md:px-12 py-12 border-t border-yellow-300 dark:border-gray-700 text-sm text-yellow-800 dark:text-yellow-300 transition-colors">
    <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8">
      <div>
        <div class="flex items-center space-x-2">
          <img src="Asset/logo1.png" alt="DompetKu Logo" class="w-5 h-5">
          <span class="text-lg font-semibold text-yellow-900 dark:text-white">DompetKu</span>
        </div>
        <p class="mt-2">Aplikasi pencatatan keuangan pribadi untuk kehidupan finansial yang lebih baik.</p>
      </div>
      <div>
        <h4 class="font-bold text-yellow-900 dark:text-white mb-2">Tentang Kami</h4>
        <ul class="space-y-1">
          <li><a href="#" class="hover:underline">Fitur DompetKu</a></li>
          <li><a href="#" class="hover:underline">Tim Developer</a></li>
          <li><a href="#" class="hover:underline">Kontak</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold text-yellow-900 dark:text-white mb-2">Bantuan</h4>
        <ul class="space-y-1">
          <li><a href="#" class="hover:underline">FAQ</a></li>
          <li><a href="#" class="hover:underline">Panduan Pengguna</a></li>
          <li><a href="#" class="hover:underline">Dukungan Teknis</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-bold text-yellow-900 dark:text-white mb-2">Kebijakan</h4>
        <ul class="space-y-1">
          <li><a href="#" class="hover:underline">Privasi</a></li>
          <li><a href="#" class="hover:underline">Syarat Layanan</a></li>
        </ul>
      </div>
    </div>
    <div class="mt-10 pt-5 text-center text-xs text-yellow-900 dark:text-gray-400">
      © 2025 Dompetku. All rights reserved.
    </div>
  </footer>

  <!-- Dark mode toggle -->
  <script>
    const toggleThemeBtn = document.getElementById('toggle-theme');
    toggleThemeBtn.addEventListener('click', () => {
      document.documentElement.classList.toggle('dark');
    });
  </script>
</body>
</html>
