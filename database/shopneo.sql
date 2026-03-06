-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 02:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopneo`
--

-- --------------------------------------------------------

--
-- Table structure for table `hutang_supplier`
--

CREATE TABLE `hutang_supplier` (
  `id` int(11) NOT NULL,
  `pembelian_id` int(11) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `sisa` decimal(12,2) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `status` enum('belum_lunas','lunas') DEFAULT 'belum_lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE `info` (
  `id` int(11) NOT NULL,
  `jumlah_produk` int(11) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `jumlah_cabang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`id`, `jumlah_produk`, `rating`, `jumlah_cabang`) VALUES
(1, 500, 4.8, 50);

-- --------------------------------------------------------

--
-- Table structure for table `outlet`
--

CREATE TABLE `outlet` (
  `id` int(11) NOT NULL,
  `nama_outlet` varchar(100) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `outlet`
--

INSERT INTO `outlet` (`id`, `nama_outlet`, `lokasi`) VALUES
(1, 'Cabang Pusat', 'Jakarta Barat'),
(2, 'Cabant Timur', 'Jakarta Timur'),
(3, 'Cabang Padang', 'Kota Padang');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `supplier` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `dibayar` decimal(12,2) DEFAULT 0.00,
  `status` enum('lunas','belum_lunas') DEFAULT 'belum_lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id`, `tanggal`, `supplier`, `total`, `dibayar`, `status`) VALUES
(2, '2025-09-11', 'Orang Baik', 250000, 0.00, 'lunas'),
(3, '2025-09-11', 'Orang Baik2', 1000000, 0.00, 'lunas'),
(4, '2025-09-11', 'PT Nugraha', 14800000, 0.00, 'lunas'),
(5, '2025-09-12', 'PT. BUAH SEGAR RAYA', 1000000, 0.00, 'lunas'),
(6, '2025-09-18', 'PT.HAPISAS', 1500000, 0.00, 'lunas'),
(7, '2025-09-25', 'PT.BUAH RAYA', 2790000, 0.00, 'lunas'),
(8, '2025-10-03', 'PT.HAPISAS', 810000, 0.00, 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian_detail`
--

CREATE TABLE `pembelian_detail` (
  `id` int(11) NOT NULL,
  `pembelian_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian_detail`
--

INSERT INTO `pembelian_detail` (`id`, `pembelian_id`, `produk_id`, `qty`, `harga`, `subtotal`) VALUES
(1, 2, 1, 10, 25000, 250000),
(2, 3, 4, 50, 20000, 1000000),
(3, 4, 1, 180, 25000, 4500000),
(4, 4, 4, 150, 20000, 3000000),
(5, 4, 3, 101, 50000, 5050000),
(6, 4, 2, 150, 15000, 2250000),
(7, 5, 4, 50, 20000, 1000000),
(8, 6, 8, 50, 30000, 1500000),
(9, 7, 8, 93, 30000, 2790000),
(10, 8, 8, 27, 30000, 810000);

-- --------------------------------------------------------

--
-- Table structure for table `piutang_pelanggan`
--

CREATE TABLE `piutang_pelanggan` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `pelanggan_id` int(11) DEFAULT NULL,
  `sisa` decimal(12,2) NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `status` enum('belum_lunas','lunas') DEFAULT 'belum_lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `gambar` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `harga`, `stok`, `gambar`) VALUES
(1, 'Buah Segar', 25000, 348, 'asset/img/buah1.jpg'),
(2, 'Sayuran Segar', 15000, 448, 'asset/img/buah2.jpg'),
(3, 'Madu Alami', 50000, 28, 'asset/img/buah3.jpg'),
(4, 'Jeruk Manis', 20000, 207, 'asset/img/buah1.jpg'),
(7, 'Pisang Binjai', 15000, 99, 'asset/img/buah4.jpg'),
(8, 'Apel merah', 30000, 126, 'asset/img/apelmerah_1758154617.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `diskon` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_akhir` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retur_pembelian`
--

CREATE TABLE `retur_pembelian` (
  `id` int(11) NOT NULL,
  `pembelian_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `alasan` text DEFAULT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retur_penjualan`
--

CREATE TABLE `retur_penjualan` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `alasan` text DEFAULT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `outlet_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `dibayar` decimal(12,2) DEFAULT 0.00,
  `tanggal` date DEFAULT NULL,
  `status` enum('lunas','dibayar','belum_lunas') DEFAULT 'belum_lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `outlet_id`, `total`, `dibayar`, `tanggal`, `status`) VALUES
(1, 1, 110000.00, 0.00, '2025-09-10', 'lunas'),
(2, 1, 110000.00, 0.00, '2025-09-10', 'lunas'),
(3, 1, 1100000.00, 0.00, '2025-09-10', 'dibayar'),
(4, 2, 5400000.00, 0.00, '2025-09-10', 'lunas'),
(5, 2, 1250000.00, 0.00, '2025-09-11', 'lunas'),
(6, 2, 1570000.00, 0.00, '2025-09-11', 'lunas'),
(7, 1, 10000000.00, 0.00, '2025-09-11', 'lunas'),
(8, 2, 13340000.00, 0.00, '2025-09-18', 'lunas'),
(9, 3, 2700000.00, 0.00, '2025-09-19', 'lunas'),
(10, 1, 1625000.00, 0.00, '2025-09-25', 'lunas'),
(11, 1, 155000.00, 0.00, '2025-10-03', 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `harga` int(11) NOT NULL,
  `subtotal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id`, `transaksi_id`, `produk_id`, `qty`, `harga`, `subtotal`) VALUES
(1, 2, 1, 1, 25000, 25000),
(2, 2, 4, 1, 20000, 20000),
(3, 2, 3, 1, 50000, 50000),
(4, 2, 2, 1, 15000, 15000),
(5, 3, 1, 10, 25000, 250000),
(6, 3, 4, 10, 20000, 200000),
(7, 3, 3, 10, 50000, 500000),
(8, 3, 2, 10, 15000, 150000),
(9, 4, 1, 50, 25000, 1250000),
(10, 4, 4, 30, 20000, 600000),
(11, 4, 3, 50, 50000, 2500000),
(12, 4, 2, 70, 15000, 1050000),
(13, 5, 1, 50, 25000, 1250000),
(14, 6, 1, 30, 25000, 750000),
(15, 6, 4, 1, 20000, 20000),
(16, 6, 3, 1, 50000, 50000),
(17, 6, 2, 50, 15000, 750000),
(18, 7, 1, 150, 25000, 3750000),
(19, 7, 4, 100, 20000, 2000000),
(20, 7, 3, 70, 50000, 3500000),
(21, 7, 2, 50, 15000, 750000),
(22, 8, 8, 3, 30000, 90000),
(23, 8, 1, 50, 25000, 1250000),
(24, 8, 4, 100, 20000, 2000000),
(25, 8, 3, 200, 50000, 10000000),
(26, 9, 8, 90, 30000, 2700000),
(27, 10, 8, 50, 30000, 1500000),
(28, 10, 1, 1, 25000, 25000),
(29, 10, 4, 1, 20000, 20000),
(30, 10, 3, 1, 50000, 50000),
(31, 10, 7, 1, 15000, 15000),
(32, 10, 2, 1, 15000, 15000),
(33, 11, 8, 1, 30000, 30000),
(34, 11, 1, 1, 25000, 25000),
(35, 11, 4, 1, 20000, 20000),
(36, 11, 3, 1, 50000, 50000),
(37, 11, 7, 1, 15000, 15000),
(38, 11, 2, 1, 15000, 15000);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_online`
--

CREATE TABLE `transaksi_online` (
  `id` int(11) NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `nama_penerima` varchar(100) DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('pending','lunas','batal') DEFAULT 'pending',
  `alamat_pengiriman` text DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_online`
--

INSERT INTO `transaksi_online` (`id`, `pelanggan_id`, `nama_penerima`, `tanggal`, `total`, `status`, `alamat_pengiriman`, `metode_pembayaran`, `keterangan`, `created_at`) VALUES
(1, 3, 'Forma Keyran', '2025-09-11 08:19:10', 25000.00, 'lunas', '', '', NULL, '2025-09-11 02:06:31'),
(2, 2, 'Testin123', '2025-09-11 08:13:06', 25000.00, 'lunas', '', '', NULL, '2025-09-11 02:08:43'),
(3, 1, 'Arel Firyosakil', '2025-09-11 01:22:45', 300000.00, 'lunas', 'Test', 'COD', NULL, '2025-09-11 02:09:25'),
(4, 4, 'Farel', '2025-09-11 10:35:53', 40000.00, 'lunas', 'Jl.Malin Deman No.10 Simpang Haru, Padang Timur, Padang', 'E-Wallet', NULL, '2025-09-11 03:36:27'),
(5, 5, 'Forma', '2025-09-11 10:46:26', 50000.00, 'lunas', 'JL.Sisingamangaraja', 'COD', NULL, '2025-09-11 03:52:18'),
(6, 7, 'Testin123', '2025-09-18 07:17:43', 70000.00, 'lunas', 'adadawasdwasd', 'E-Wallet', NULL, '2025-09-18 00:18:10'),
(7, 8, 'hapis', '2025-09-18 08:36:17', 48500.00, 'lunas', 'adsadadasd', 'E-Wallet', NULL, '2025-09-18 01:36:48'),
(8, 9, 'Fahrel', '2025-09-24 10:45:26', 25800.00, 'lunas', 'Padang Timur', 'E-Wallet', NULL, '2025-09-24 03:45:47'),
(9, 10, 'Testin123', '2025-09-25 08:23:11', 19000.00, 'lunas', 'addwasdwasd', 'Transfer', NULL, '2025-09-25 01:23:29'),
(10, 11, 'adjaslk', '2025-09-29 08:25:50', 25800.00, 'lunas', 'ajdlakjdjsjdld', 'E-Wallet', NULL, '2025-09-29 01:26:32'),
(11, 13, 'dwasdwa', '2025-10-02 19:06:18', 48500.00, 'lunas', 'dadadwa', 'E-Wallet', NULL, '2025-10-03 00:42:35');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_pelanggan`
--

CREATE TABLE `transaksi_pelanggan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `metode_bayar` varchar(50) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `status` enum('pending','diproses','selesai','batal') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_pelanggan`
--

INSERT INTO `transaksi_pelanggan` (`id`, `user_id`, `nama_penerima`, `alamat`, `metode_bayar`, `total`, `tanggal`, `status`) VALUES
(1, 8, 'Arel Firyosakil', 'Test', 'COD', 300000.00, '2025-09-11 01:22:45', 'selesai'),
(2, 8, 'Testin123', 'test123', 'E-Wallet', 25000.00, '2025-09-11 08:13:06', 'selesai'),
(3, 8, 'Forma Keyran', 'Cepat bet', 'COD', 25000.00, '2025-09-11 08:19:10', 'selesai'),
(4, 8, 'Farel', 'Jl.Malin Deman No.10 Simpang Haru, Padang Timur, Padang', 'E-Wallet', 40000.00, '2025-09-11 10:35:53', 'selesai'),
(5, 8, 'Forma', 'JL.Sisingamangaraja', 'COD', 50000.00, '2025-09-11 10:46:26', 'selesai'),
(6, 8, 'Forma Keyran', 'JL.Kemakmuran Indonesia 2045', 'COD', 25000.00, '2025-09-12 07:25:21', 'selesai'),
(7, 8, 'Testin123', 'adadawasdwasd', 'E-Wallet', 70000.00, '2025-09-18 07:17:43', 'selesai'),
(8, 8, 'hapis', 'adsadadasd', 'E-Wallet', 48500.00, '2025-09-18 08:36:17', 'selesai'),
(9, 8, 'Fahrel', 'Padang Timur', 'E-Wallet', 25800.00, '2025-09-24 10:45:26', 'selesai'),
(10, 8, 'Testin123', 'addwasdwasd', 'Transfer', 19000.00, '2025-09-25 08:23:11', 'selesai'),
(11, 8, 'adjaslk', 'ajdlakjdjsjdld', 'E-Wallet', 25800.00, '2025-09-29 08:25:50', 'selesai'),
(12, 8, 'adwadwasdw', 'adwasdwa', 'E-Wallet', 50000.00, '2025-10-02 17:13:01', 'selesai'),
(13, 8, 'dwasdwa', 'dadadwa', 'E-Wallet', 48500.00, '2025-10-02 19:06:18', 'selesai'),
(14, 14, 'Heru', 'adkjahdjhdsadkh', 'COD', 315000.00, '2025-11-13 07:55:39', 'pending'),
(15, 8, 'ghj', 'fhfhff', 'COD', 25000.00, '2025-11-16 13:39:49', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_pelanggan_detail`
--

CREATE TABLE `transaksi_pelanggan_detail` (
  `id` int(11) NOT NULL,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `nama_produk` varchar(150) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_pelanggan_detail`
--

INSERT INTO `transaksi_pelanggan_detail` (`id`, `transaksi_id`, `produk_id`, `nama_produk`, `qty`, `harga`, `subtotal`) VALUES
(1, 1, 1, 'Buah Segar', 12, 25000.00, 300000.00),
(2, 2, 1, 'Buah Segar', 1, 25000.00, 25000.00),
(3, 3, 1, 'Buah Segar', 1, 25000.00, 25000.00),
(4, 4, 2, 'Sayuran Segar', 1, 15000.00, 15000.00),
(5, 4, 1, 'Buah Segar', 1, 25000.00, 25000.00),
(6, 5, 1, 'Buah Segar', 2, 25000.00, 50000.00),
(7, 6, 1, 'Buah Segar', 1, 25000.00, 25000.00),
(8, 7, 1, 'Buah Segar', 1, 25000.00, 25000.00),
(9, 7, 8, 'Apel merah', 1, 30000.00, 30000.00),
(10, 7, 7, 'Pisang Binjai', 1, 15000.00, 15000.00),
(11, 8, 3, 'Madu Alami', 1, 48500.00, 48500.00),
(12, 9, 8, 'Apel merah', 1, 25800.00, 25800.00),
(13, 10, 4, 'Jeruk Manis', 1, 19000.00, 19000.00),
(14, 11, 8, 'Apel merah', 1, 25800.00, 25800.00),
(15, 12, 3, 'Madu Alami', 1, 50000.00, 50000.00),
(16, 13, 3, 'Madu Alami', 1, 48500.00, 48500.00),
(17, 14, 2, 'Sayuran Segar', 21, 15000.00, 315000.00),
(18, 15, 1, 'Buah Segar', 1, 25000.00, 25000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir','pelanggan') DEFAULT 'pelanggan',
  `outlet_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `outlet_id`) VALUES
(1, 'Pelanggan1', 'pelanggan1@gmail.com', '$2y$10$F8tLZ67cFeXtOl0qJs9ZyO3BnIfC/FTQkNJB51sY13hzOXPmO9ROm', 'pelanggan', NULL),
(2, 'Admin', 'admin@gmail.com', 'admin123', 'admin', NULL),
(3, 'Admin2', 'admin12@gmail.com', '$2y$10$//M0fy2zSlSOcrZvIOF/luVOrXkwxPzw1ooIYMboEc0IlWP5ALpl2', 'admin', NULL),
(7, 'kasir3', 'kasir3@gmail.com', '$2y$10$1N4l6aq9/XQz80Uhy0p5UOC8Sv8czqmAqPbvhoq0pO7j746ZGNIzC', 'kasir', 1),
(8, 'farel', 'farel@gmail.com', '$2y$10$ZSXvtzG5c/T.JXMdQtsXguywYFWFg9xe.EykPmOMh/aqc3LW/fV/G', 'pelanggan', NULL),
(9, 'kasir1', '', '$2y$10$cBF2/18GOtppX3JnoXt9P..CptJG50DqxGi0rfE14okrDcf7wMKSy', 'kasir', 2),
(10, 'Padang1', 'padang@gmail.com', '$2y$10$d8BMjF9oS/je/0smf/jBYurn1.Fi.yUGZ.rrD5W/K/5N4RqvQvPAa', 'kasir', 3),
(11, 'Kael', 'kael@gmail.com', '$2y$10$TZuTMT1IxnBLnDjUdWkPp.ZsllYdK9C4jf8WYYojKOrV.xnPF9QpS', 'pelanggan', NULL),
(12, 'Kasirtampan', 'tampan123@gmail.com', '$2y$10$qY/h85ZvW5N/YhnDH9ejJex4n76OfaTsC9lAAUPZw17dz.iUN4aEy', 'kasir', 2),
(13, 'kasir4', 'kasir4@gmail.com', '$2y$10$wt8sDftSa6SVOwwGCJwmyOgWPM0UYNr94oCw5lMDaanTI81QYpA5G', 'kasir', 2),
(14, 'heru', 'heru@gmail.com', '$2y$10$Ndqq57tZmKgMzeqASzDQEet3mqwLDA4p0yiGfkEnvW3WVTdqUxylm', 'pelanggan', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hutang_supplier`
--
ALTER TABLE `hutang_supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_id` (`pembelian_id`);

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outlet`
--
ALTER TABLE `outlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_id` (`pembelian_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `piutang_pelanggan`
--
ALTER TABLE `piutang_pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `retur_pembelian`
--
ALTER TABLE `retur_pembelian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_id` (`pembelian_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `retur_penjualan`
--
ALTER TABLE `retur_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `transaksi_online`
--
ALTER TABLE `transaksi_online`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_pelanggan`
--
ALTER TABLE `transaksi_pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaksi_pelanggan_detail`
--
ALTER TABLE `transaksi_pelanggan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_id` (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_outlet` (`outlet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hutang_supplier`
--
ALTER TABLE `hutang_supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `info`
--
ALTER TABLE `info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `outlet`
--
ALTER TABLE `outlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `piutang_pelanggan`
--
ALTER TABLE `piutang_pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `retur_pembelian`
--
ALTER TABLE `retur_pembelian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retur_penjualan`
--
ALTER TABLE `retur_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `transaksi_online`
--
ALTER TABLE `transaksi_online`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transaksi_pelanggan`
--
ALTER TABLE `transaksi_pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transaksi_pelanggan_detail`
--
ALTER TABLE `transaksi_pelanggan_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hutang_supplier`
--
ALTER TABLE `hutang_supplier`
  ADD CONSTRAINT `hutang_supplier_ibfk_1` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`);

--
-- Constraints for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  ADD CONSTRAINT `pembelian_detail_ibfk_1` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembelian_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `piutang_pelanggan`
--
ALTER TABLE `piutang_pelanggan`
  ADD CONSTRAINT `piutang_pelanggan_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`);

--
-- Constraints for table `promo`
--
ALTER TABLE `promo`
  ADD CONSTRAINT `promo_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `retur_pembelian`
--
ALTER TABLE `retur_pembelian`
  ADD CONSTRAINT `retur_pembelian_ibfk_1` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`),
  ADD CONSTRAINT `retur_pembelian_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `retur_penjualan`
--
ALTER TABLE `retur_penjualan`
  ADD CONSTRAINT `retur_penjualan_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`),
  ADD CONSTRAINT `retur_penjualan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `transaksi_pelanggan`
--
ALTER TABLE `transaksi_pelanggan`
  ADD CONSTRAINT `transaksi_pelanggan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi_pelanggan_detail`
--
ALTER TABLE `transaksi_pelanggan_detail`
  ADD CONSTRAINT `transaksi_pelanggan_detail_ibfk_1` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi_pelanggan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_pelanggan_detail_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_outlet` FOREIGN KEY (`outlet_id`) REFERENCES `outlet` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
