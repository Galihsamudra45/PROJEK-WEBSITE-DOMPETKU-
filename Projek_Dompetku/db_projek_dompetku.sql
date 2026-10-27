-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Jun 2025 pada 11.04
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_projek_dompetku`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_hapus`
--

CREATE TABLE `pengajuan_hapus` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `alasan` text DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') DEFAULT 'menunggu',
  `tanggal_pengajuan` date DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan_hapus`
--

INSERT INTO `pengajuan_hapus` (`id`, `user_id`, `alasan`, `status`, `tanggal_pengajuan`) VALUES
(1, 1, 'Sudah Bosan', 'menunggu', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `judul` varchar(100) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `tanggal_dibuat` date DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `tanggal_dibuat`, `gambar`) VALUES
(1, '📢 Selamat Datang di Dompetku!', 'Halo pengguna baru 👋,\r\n\r\nSelamat datang di Dompetku, sistem pencatatan keuangan pribadi yang membantu kamu mengelola pemasukan dan pengeluaran secara mudah, aman, dan teratur!\r\n\r\n🔐 Fitur-fitur yang bisa kamu nikmati:\r\n💰 Pencatatan transaksi harian\r\n📊 Grafik pemasukan & pengeluaran otomatis\r\n📝 Riwayat transaksi lengkap\r\n🧾 Pengajuan penghapusan akun jika diperlukan\r\n🧑‍💼 Data biodata pengguna\r\n📱 Tampilan responsif di semua perangkat\r\n\r\n💡 Tips:\r\nMulailah dengan mengisi data biodata dan menambahkan transaksi pertamamu agar saldo kamu bisa dihitung secara otomatis.\r\n\r\nTerima kasih telah bergabung bersama kami. Yuk, kelola keuanganmu mulai sekarang dengan lebih bijak bersama Dompetku! 💛', '2025-06-13', 'uploads/1749785433_gambar1 (9).png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `jenis`, `kategori`, `jumlah`, `keterangan`, `tanggal`, `waktu`) VALUES
(1, 1, 'pemasukan', 'gaji', 10000.00, '', '2025-06-12', '10:18:00'),
(2, 1, 'pengeluaran', 'makan', 5000.00, '', '2025-06-12', '10:25:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `saldo` decimal(15,2) DEFAULT 0.00,
  `status_akun` enum('aktif','nonaktif') DEFAULT 'aktif',
  `tanggal_daftar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`, `email`, `tempat_lahir`, `tanggal_lahir`, `pekerjaan`, `saldo`, `status_akun`, `tanggal_daftar`) VALUES
(1, 'Jaka123', '$2y$10$RxEN4c9jEcLZnbJzA2eprO3VRCTRjZJ4gDq9Am3Rtjow19UjZyVgq', 'user', 'jaka handaka', 'joko@gmail.com', 'Jombang', '2004-04-09', 'pelajar', 0.00, 'aktif', '2025-06-12 11:01:37'),
(4, 'admin123', '$2y$10$jpBMtTlm4Gd4YNfazkkX3O2CIlBIgkWfpb/5tUxMSig4bIdWmiGp6', 'admin', '', '', '', NULL, '', 0.00, 'aktif', '2025-06-12 11:01:37');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pengajuan_hapus`
--
ALTER TABLE `pengajuan_hapus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pengajuan_hapus`
--
ALTER TABLE `pengajuan_hapus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengajuan_hapus`
--
ALTER TABLE `pengajuan_hapus`
  ADD CONSTRAINT `pengajuan_hapus_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
