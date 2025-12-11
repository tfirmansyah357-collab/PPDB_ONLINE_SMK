-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Des 2025 pada 05.15
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
-- Database: `db_ppdb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `gelombang`
--

CREATE TABLE `gelombang` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `tahun` varchar(20) DEFAULT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `biaya_daftar` decimal(12,2) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `gelombang`
--

INSERT INTO `gelombang` (`id`, `nama`, `tahun`, `tgl_mulai`, `tgl_selesai`, `biaya_daftar`, `aktif`) VALUES
(1, 'Gelombang 1', '2025/2026', '2025-09-01', '2025-12-01', 550000.00, 1),
(2, 'Gelombang 2', '2025/2026', '2026-01-01', '2026-05-01', 650000.00, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurusan`
--

CREATE TABLE `jurusan` (
  `id` int(11) NOT NULL,
  `kode` varchar(10) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kuota` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jurusan`
--

INSERT INTO `jurusan` (`id`, `kode`, `nama`, `kuota`) VALUES
(1, 'RPL', 'Rekayasa Perangkat Lunak', 50),
(2, 'ANM', 'Animasi', 30),
(3, 'AKT', 'Akuntansi Keuangan dan Lembaga', 30),
(4, 'PMS', 'Pemasaran', 20),
(5, 'DKV', 'Desain Komunikasi Visual', 20),
(6, 'TKJ', 'Teknik Komputer Jaringan', 50);

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aksi` varchar(100) DEFAULT NULL,
  `objek` varchar(100) DEFAULT NULL,
  `objek_id` bigint(20) DEFAULT NULL,
  `waktu` datetime DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint(20) NOT NULL,
  `pendaftar_id` bigint(20) DEFAULT NULL,
  `nominal` decimal(12,2) DEFAULT NULL,
  `nama_pengirim` varchar(100) DEFAULT NULL,
  `bank_pengirim` varchar(50) DEFAULT NULL,
  `url_bukti_transfer` varchar(255) DEFAULT NULL,
  `tgl_upload` datetime DEFAULT NULL,
  `status` enum('Pending','Lunas','Ditolak') DEFAULT 'Pending',
  `verifikator_id` int(11) DEFAULT NULL,
  `tgl_verifikasi` datetime DEFAULT NULL,
  `catatan_verifikasi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `pendaftar_id`, `nominal`, `nama_pengirim`, `bank_pengirim`, `url_bukti_transfer`, `tgl_upload`, `status`, `verifikator_id`, `tgl_verifikasi`, `catatan_verifikasi`) VALUES
(1, 1, 550000.00, 'TaufikFirmansyah', 'DANA', 'http://localhost/ppdb/uploads/bukti_bayar/bayar_1_Qris.jpg', '2025-11-18 09:43:51', 'Lunas', 3, '2025-11-18 09:54:13', 'Diverifikasi Lunas'),
(2, 4, 550000.00, 'taufik formansyah', 'gopay', 'http://localhost/ppdb/uploads/bukti_bayar/bayar_4_buktipembayaran.jpeg', '2025-11-20 11:23:43', 'Lunas', 3, '2025-11-20 11:26:33', 'Diverifikasi Lunas'),
(3, 5, 550000.00, 'taufik formansyah', 'mandiri', 'http://localhost/ppdb/uploads/bukti_bayar/bayar_5_buktipembayaran.jpeg', '2025-11-21 10:36:15', 'Lunas', 3, '2025-11-21 10:36:51', 'Diverifikasi Lunas'),
(4, 6, 550000.00, 'siswa', 'dana', 'http://localhost/ppdb/uploads/bukti_bayar/bayar_6_banner all.jpg', '2025-11-26 12:39:31', 'Lunas', 3, '2025-11-26 12:51:04', 'Diverifikasi Lunas'),
(5, 7, 550000.00, 'taufik', 'dana', 'http://localhost/ppdb/uploads/bukti_bayar/bayar_7_pembayaran.jpg', '2025-11-26 13:08:02', 'Lunas', 3, '2025-11-26 13:09:24', 'Diverifikasi Lunas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftar`
--

CREATE TABLE `pendaftar` (
  `id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tanggal_daftar` datetime DEFAULT NULL,
  `no_pendaftaran` varchar(20) DEFAULT NULL,
  `gelombang_id` int(11) DEFAULT NULL,
  `jurusan_id` int(11) DEFAULT NULL,
  `status_administrasi` enum('Pending','Diverifikasi','Ditolak') DEFAULT 'Pending',
  `admin_verifikator_id` int(11) DEFAULT NULL,
  `tgl_verifikasi_adm` datetime DEFAULT NULL,
  `catatan_adm` text DEFAULT NULL,
  `status_pembayaran` enum('Belum Bayar','Pending Verifikasi','Lunas','Ditolak') DEFAULT 'Belum Bayar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftar`
--

INSERT INTO `pendaftar` (`id`, `user_id`, `tanggal_daftar`, `no_pendaftaran`, `gelombang_id`, `jurusan_id`, `status_administrasi`, `admin_verifikator_id`, `tgl_verifikasi_adm`, `catatan_adm`, `status_pembayaran`) VALUES
(1, 5, '2025-11-18 09:42:59', 'PPDB-2025-0005', 1, 1, 'Diverifikasi', 1, '2025-11-18 09:53:37', 'Berkas valid', 'Lunas'),
(2, 7, '2025-11-18 11:36:47', 'PPDB-2025-0007', 1, 1, 'Ditolak', 6, '2025-11-18 11:38:43', 'Berkas tidak lengkap', 'Belum Bayar'),
(3, 9, '2025-11-20 07:29:19', 'PPDB-2025-0009', 1, 1, 'Diverifikasi', 6, '2025-11-20 07:30:07', 'Berkas valid', 'Belum Bayar'),
(4, 10, '2025-11-20 11:22:31', 'PPDB-2025-0010', 1, 1, 'Diverifikasi', 6, '2025-11-20 11:25:48', 'Berkas valid', 'Lunas'),
(5, 12, '2025-11-21 10:35:20', 'PPDB-2025-0012', 1, 2, 'Diverifikasi', 6, '2025-11-21 10:42:48', 'Berkas valid', 'Lunas'),
(6, 2, '2025-11-26 12:36:03', 'PPDB-2025-0002', 1, 5, 'Diverifikasi', 6, '2025-11-26 12:44:46', 'Berkas valid', 'Lunas'),
(7, 14, '2025-11-26 13:07:28', 'PPDB-2025-0014', 1, 1, 'Diverifikasi', 6, '2025-11-26 13:08:44', 'Berkas valid', 'Lunas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftar_asal_sekolah`
--

CREATE TABLE `pendaftar_asal_sekolah` (
  `pendaftar_id` bigint(20) NOT NULL,
  `nama_sekolah` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftar_asal_sekolah`
--

INSERT INTO `pendaftar_asal_sekolah` (`pendaftar_id`, `nama_sekolah`) VALUES
(1, 'smpn3rancaekek'),
(2, 'smp4rancaekek'),
(3, 'smpn1global'),
(4, 'SMPN 1 Jatinngor'),
(5, 'smpn1global'),
(6, 'smpn1cilenyi'),
(7, 'smpn3rancaekek');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftar_berkas`
--

CREATE TABLE `pendaftar_berkas` (
  `id` bigint(20) NOT NULL,
  `pendaftar_id` bigint(20) DEFAULT NULL,
  `jenis` enum('RAPOR','AKTA','KK','LAINNYA') DEFAULT NULL,
  `nama_file` varchar(255) DEFAULT NULL,
  `url_file` varchar(255) DEFAULT NULL,
  `ukuran_kb` int(11) DEFAULT NULL,
  `valid` tinyint(4) DEFAULT 0,
  `catatan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftar_berkas`
--

INSERT INTO `pendaftar_berkas` (`id`, `pendaftar_id`, `jenis`, `nama_file`, `url_file`, `ukuran_kb`, `valid`, `catatan`) VALUES
(1, 1, 'RAPOR', '1_RAPOR_Screenshot 2025-11-18 093504.png', 'http://localhost/ppdb/uploads/berkas/1_RAPOR_Screenshot 2025-11-18 093504.png', 344, 0, NULL),
(2, 1, 'AKTA', '1_AKTA_Screenshot 2025-11-18 085611.png', 'http://localhost/ppdb/uploads/berkas/1_AKTA_Screenshot 2025-11-18 085611.png', 72, 0, NULL),
(3, 1, 'KK', '1_KK_Screenshot 2025-11-17 114514.png', 'http://localhost/ppdb/uploads/berkas/1_KK_Screenshot 2025-11-17 114514.png', 46, 0, NULL),
(4, 2, 'RAPOR', '2_RAPOR_Screenshot 2025-11-18 112441.png', 'http://localhost/ppdb/uploads/berkas/2_RAPOR_Screenshot 2025-11-18 112441.png', 2119, 0, NULL),
(5, 2, 'AKTA', '2_AKTA_Screenshot 2025-07-19 171950.png', 'http://localhost/ppdb/uploads/berkas/2_AKTA_Screenshot 2025-07-19 171950.png', 213, 0, NULL),
(6, 2, 'KK', '2_KK_download.jpeg', 'http://localhost/ppdb/uploads/berkas/2_KK_download.jpeg', 8, 0, NULL),
(7, 3, 'RAPOR', '3_RAPOR_baknus.jpeg', 'http://localhost/ppdb/uploads/berkas/3_RAPOR_baknus.jpeg', 11, 0, NULL),
(8, 3, 'AKTA', '3_AKTA_perpustakaan.jpeg', 'http://localhost/ppdb/uploads/berkas/3_AKTA_perpustakaan.jpeg', 9, 0, NULL),
(9, 3, 'KK', '3_KK_studd.jpeg', 'http://localhost/ppdb/uploads/berkas/3_KK_studd.jpeg', 8, 0, NULL),
(10, 4, 'RAPOR', '4_RAPOR_rapot.jpg', 'http://localhost/ppdb/uploads/berkas/4_RAPOR_rapot.jpg', 155, 0, NULL),
(11, 4, 'AKTA', '4_AKTA_akta.jpg', 'http://localhost/ppdb/uploads/berkas/4_AKTA_akta.jpg', 71, 0, NULL),
(12, 4, 'KK', '4_KK_kartu KK.jpg', 'http://localhost/ppdb/uploads/berkas/4_KK_kartu KK.jpg', 161, 0, NULL),
(13, 5, 'RAPOR', '5_RAPOR_rapot.jpg', 'http://localhost/ppdb/uploads/berkas/5_RAPOR_rapot.jpg', 155, 0, NULL),
(14, 5, 'AKTA', '5_AKTA_akta.jpg', 'http://localhost/ppdb/uploads/berkas/5_AKTA_akta.jpg', 71, 0, NULL),
(15, 5, 'KK', '5_KK_kartu KK.jpg', 'http://localhost/ppdb/uploads/berkas/5_KK_kartu KK.jpg', 161, 0, NULL),
(16, 6, 'RAPOR', '6_RAPOR_foto.jpeg', 'http://localhost/ppdb/uploads/berkas/6_RAPOR_foto.jpeg', 22, 0, NULL),
(17, 6, 'AKTA', '6_AKTA_banner all.jpg', 'http://localhost/ppdb/uploads/berkas/6_AKTA_banner all.jpg', 1420, 0, NULL),
(18, 6, 'KK', '6_KK_sample.JPG', 'http://localhost/ppdb/uploads/berkas/6_KK_sample.JPG', 3352, 0, NULL),
(19, 7, 'RAPOR', '7_RAPOR_raport.png', 'http://localhost/ppdb/uploads/berkas/7_RAPOR_raport.png', 7, 0, NULL),
(20, 7, 'AKTA', '7_AKTA_akta.jpg', 'http://localhost/ppdb/uploads/berkas/7_AKTA_akta.jpg', 11, 0, NULL),
(21, 7, 'KK', '7_KK_KK.jpg', 'http://localhost/ppdb/uploads/berkas/7_KK_KK.jpg', 10, 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftar_data_ortu`
--

CREATE TABLE `pendaftar_data_ortu` (
  `pendaftar_id` bigint(20) NOT NULL,
  `nama_ayah` varchar(120) DEFAULT NULL,
  `nama_ibu` varchar(120) DEFAULT NULL,
  `no_handphone_ortu` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftar_data_ortu`
--

INSERT INTO `pendaftar_data_ortu` (`pendaftar_id`, `nama_ayah`, `nama_ibu`, `no_handphone_ortu`) VALUES
(1, 'yanto', 'yanti', '0891234567890'),
(2, 'haryanto', 'haryanti', '67890123456'),
(3, 'dimas', 'riska', '89123456789'),
(4, 'dimas argawiduyatama', 'sentri dinimanipuloh', '089765746354'),
(5, 'dimas', 'yanti', '08912345678'),
(6, 'naupal', 'tiara', '08612345678'),
(7, 'yanto', 'yanti', '089123456780');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendaftar_data_siswa`
--

CREATE TABLE `pendaftar_data_siswa` (
  `pendaftar_id` bigint(20) NOT NULL,
  `nama_lengkap` varchar(120) DEFAULT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `no_handphone` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `wilayah_asal_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendaftar_data_siswa`
--

INSERT INTO `pendaftar_data_siswa` (`pendaftar_id`, `nama_lengkap`, `nis`, `no_handphone`, `email`, `wilayah_asal_id`) VALUES
(1, 'taufik firmansyah', '1234567890', '08123456789', 'taufik@gmail.com', 3),
(2, 'opikbn', '123456', '1234567890098', 'tfirmansyah357@gmail.com', 5),
(3, 'yakult', '567890123456', '089123456789', 'botol123@gmail.com', 2),
(4, 'reva oktaviana zahran', '1324567890', '089513538574', 'revaoktaviana213@gmail.com', 6),
(5, 'siswa demo', '23241010008', '089123456789', 'siswademo123@gmail.com', 1),
(6, 'Siswa Demo', '23241010008', '0891234567890', 'siswa@ppdb.com', 2),
(7, 'oneng komariah', '23241010008', '0891245678', 'siswa1@gmail.com', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `hp` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('pendaftar','admin','keuangan','kepsek','panitia') NOT NULL,
  `aktif` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `email`, `hp`, `password_hash`, `role`, `aktif`) VALUES
(1, 'Admin Utama', 'admin@ppdb.com', '08123001', '$2y$10$qBdHhJSZ8dVA8bRqADVx7erA9JqC.BF84uUpkuoLwIeJVfuLkg1qK', 'admin', 1),
(2, 'Siswa Demo', 'siswa@ppdb.com', '08123002', '$2y$10$uqB4jMGmPfR2Vd7DF6PB2etc9Jls/n4ok.CF1C.uLxMWsPFiAALKC', 'pendaftar', 1),
(3, 'Staf Keuangan', 'keuangan@ppdb.com', '08123003', '$2y$10$WR/jRG5FM8f5rmPCZpsL5O9UxNw1hS3RJsJw427w8RD0wvIU0UPRW', 'keuangan', 1),
(4, 'Kepala Sekolah', 'kepsek@ppdb.com', '08123004', '$2y$10$Hg.bIkpUAE/qFeQewOohu.TQOD2Fk0zTg1MpoWZr/lC7W9MyqiXA6', 'kepsek', 1),
(5, 'taufik firmansyah', 'taufik@gmail.com', NULL, '$2y$10$J3CzMW1XU8Fq6/bkCWlnd.QVIwZz3od2uNFBcYivTR/XvwuuybQ0i', 'pendaftar', 1),
(6, 'Staf Panitia', 'panitia@ppdb.com', '08123005', '$2y$10$VHkj27A.6lckwDASWwiSYeqjCRCqaciIlajYjydSdBMEsTR0cbW6.', 'panitia', 1),
(7, 'opikbn', 'tfirmansyah357@gmail.com', NULL, '$2y$10$73Z2NVXKz/jMWSWPXCQs2Oa2unvebTX/CIXHf1m/HY95ppZCZTEoy', 'pendaftar', 1),
(8, 'alfatih', 'alfatih123@gmail.com', NULL, '$2y$10$8BGnbiAewkieLrsph1G6sO.2B18u9tcMGiPeBCmYT5C0qhuCkHNTu', 'pendaftar', 1),
(9, 'yakult', 'botol123@gmail.com', NULL, '$2y$10$sEpfaA/o7HCbdX7iLKGbvOkPUMlWd3Ayr4NlzSbIxG6YDbLHq5gka', 'pendaftar', 1),
(10, 'reva oktaviana zahran', 'revaoktaviana213@gmail.com', NULL, '$2y$10$zfi0nkz4PNBaOq7RnndrTesjSizA3GP/BNUhPsiOV8qVpo.zfyw1O', 'pendaftar', 1),
(11, 'suprianto', 'suprianto@gmail.com', NULL, '$2y$10$lo0ciNxHY1o.IS.gmW9OReJNosSg5FYiMTdBjtkGRIlQgGQLqBcBO', 'pendaftar', 1),
(12, 'siswa demo', 'siswademo123@gmail.com', NULL, '$2y$10$f62B/gftStTOIDRo3NoHAuLy/cHHFEg1zR/eX9qm0qyKSD3sKkGTG', 'pendaftar', 1),
(13, 'haikal', 'haikal@gmail.com', NULL, '$2y$10$.TviR.WiQ61DapGEMwZt7O0Ksc0eNsDcrCHNnxmFt0A2SKt4VlXm2', 'pendaftar', 1),
(14, 'siswa1', 'siswa1@gmail.com', NULL, '$2y$10$y3uo.7h2nORv3TekJYufXuZmhVs8eXaZdRh9mziaLejz9cmoHXh3O', 'pendaftar', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ref_wilayah_asal`
--

CREATE TABLE `ref_wilayah_asal` (
  `id` int(11) NOT NULL,
  `nama_wilayah` varchar(100) DEFAULT NULL,
  `kelompok` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `ref_wilayah_asal`
--

INSERT INTO `ref_wilayah_asal` (`id`, `nama_wilayah`, `kelompok`) VALUES
(1, 'Bandung', 'Bandung Raya'),
(2, 'Cimahi', 'Bandung Raya'),
(3, 'Garut', 'Luar Bandung'),
(4, 'Cileunyi', 'Bandung Raya'),
(5, 'Luar Bandung Raya', 'Luar Bandung'),
(6, 'Luar Jawa Barat', 'Luar Jawa Barat');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `gelombang`
--
ALTER TABLE `gelombang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`,`waktu`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pendaftar_id` (`pendaftar_id`),
  ADD KEY `verifikator_id` (`verifikator_id`),
  ADD KEY `status` (`status`);

--
-- Indeks untuk tabel `pendaftar`
--
ALTER TABLE `pendaftar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_pendaftaran` (`no_pendaftaran`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `gelombang_id` (`gelombang_id`),
  ADD KEY `jurusan_id` (`jurusan_id`),
  ADD KEY `admin_verifikator_id` (`admin_verifikator_id`),
  ADD KEY `status_administrasi` (`status_administrasi`),
  ADD KEY `status_pembayaran` (`status_pembayaran`);

--
-- Indeks untuk tabel `pendaftar_asal_sekolah`
--
ALTER TABLE `pendaftar_asal_sekolah`
  ADD PRIMARY KEY (`pendaftar_id`);

--
-- Indeks untuk tabel `pendaftar_berkas`
--
ALTER TABLE `pendaftar_berkas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pendaftar_id` (`pendaftar_id`,`jenis`);

--
-- Indeks untuk tabel `pendaftar_data_ortu`
--
ALTER TABLE `pendaftar_data_ortu`
  ADD PRIMARY KEY (`pendaftar_id`);

--
-- Indeks untuk tabel `pendaftar_data_siswa`
--
ALTER TABLE `pendaftar_data_siswa`
  ADD PRIMARY KEY (`pendaftar_id`),
  ADD KEY `wilayah_asal_id` (`wilayah_asal_id`);

--
-- Indeks untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role`);

--
-- Indeks untuk tabel `ref_wilayah_asal`
--
ALTER TABLE `ref_wilayah_asal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `gelombang`
--
ALTER TABLE `gelombang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pendaftar`
--
ALTER TABLE `pendaftar`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pendaftar_berkas`
--
ALTER TABLE `pendaftar_berkas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `ref_wilayah_asal`
--
ALTER TABLE `ref_wilayah_asal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pengguna` (`id`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`verifikator_id`) REFERENCES `pengguna` (`id`);

--
-- Ketidakleluasaan untuk tabel `pendaftar`
--
ALTER TABLE `pendaftar`
  ADD CONSTRAINT `pendaftar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `pendaftar_ibfk_2` FOREIGN KEY (`gelombang_id`) REFERENCES `gelombang` (`id`),
  ADD CONSTRAINT `pendaftar_ibfk_3` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`),
  ADD CONSTRAINT `pendaftar_ibfk_4` FOREIGN KEY (`admin_verifikator_id`) REFERENCES `pengguna` (`id`);

--
-- Ketidakleluasaan untuk tabel `pendaftar_asal_sekolah`
--
ALTER TABLE `pendaftar_asal_sekolah`
  ADD CONSTRAINT `pendaftar_asal_sekolah_ibfk_1` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`);

--
-- Ketidakleluasaan untuk tabel `pendaftar_berkas`
--
ALTER TABLE `pendaftar_berkas`
  ADD CONSTRAINT `pendaftar_berkas_ibfk_1` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`);

--
-- Ketidakleluasaan untuk tabel `pendaftar_data_ortu`
--
ALTER TABLE `pendaftar_data_ortu`
  ADD CONSTRAINT `pendaftar_data_ortu_ibfk_1` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`);

--
-- Ketidakleluasaan untuk tabel `pendaftar_data_siswa`
--
ALTER TABLE `pendaftar_data_siswa`
  ADD CONSTRAINT `pendaftar_data_siswa_ibfk_1` FOREIGN KEY (`pendaftar_id`) REFERENCES `pendaftar` (`id`),
  ADD CONSTRAINT `pendaftar_data_siswa_ibfk_2` FOREIGN KEY (`wilayah_asal_id`) REFERENCES `ref_wilayah_asal` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
