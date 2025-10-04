-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Okt 2025 pada 16.15
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
-- Database: `money-tracker`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('pemasukan','pengeluaran') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Gaji Bulanan', 'pemasukan', '2025-07-12 09:15:29', '2025-07-12 09:15:29'),
(2, 'Bensin', 'pengeluaran', '2025-07-12 09:16:03', '2025-07-12 09:16:21'),
(3, 'Gaji Proyek', 'pemasukan', '2025-07-12 09:16:52', '2025-07-12 09:16:52'),
(4, 'Makan', 'pengeluaran', '2025-07-12 09:17:16', '2025-07-12 09:17:16'),
(6, 'Belanja Offline (Jajan, Beli barang, dll)', 'pengeluaran', '2025-07-31 05:45:42', '2025-07-31 05:45:42'),
(7, 'Nongkrong (Cafe, Nugas, Dll)', 'pengeluaran', '2025-07-31 05:53:21', '2025-07-31 05:53:21'),
(8, 'Debitur (Berhutang)', 'pemasukan', '2025-09-01 06:01:57', '2025-09-01 06:01:57'),
(9, 'Kreditur (Menghutangi)', 'pengeluaran', '2025-09-02 08:33:41', '2025-09-02 08:33:41'),
(10, 'Pembayaran Hutang', 'pengeluaran', '2025-09-08 00:32:42', '2025-09-08 00:32:42'),
(11, 'Hutang Dibayar', 'pengeluaran', '2025-09-08 06:27:24', '2025-09-08 06:27:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `debt_records`
--

CREATE TABLE `debt_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('Belum bayar','Lunas') NOT NULL DEFAULT 'Belum bayar',
  `jenis_hutang` enum('Kontrak','Individu') NOT NULL,
  `nama_pemberi_hutang` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `tanggal_hutang` date NOT NULL,
  `tanggal_rencana_bayar` date DEFAULT NULL,
  `money_placing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `debt_records`
--

INSERT INTO `debt_records` (`id`, `amount`, `status`, `jenis_hutang`, `nama_pemberi_hutang`, `keterangan`, `tanggal_hutang`, `tanggal_rencana_bayar`, `money_placing_id`, `created_at`, `updated_at`, `user_id`) VALUES
(46, 5000.00, 'Belum bayar', 'Individu', 'Bagus', 'tes tes', '2025-09-14', '2025-09-19', 8, '2025-09-14 09:04:10', '2025-09-14 09:04:10', 2),
(47, 5000.00, 'Belum bayar', 'Individu', 'Budieh', 'rew', '2025-09-14', '2025-10-11', 8, '2025-09-14 09:06:19', '2025-09-14 09:06:19', 2),
(49, 11000.00, 'Belum bayar', 'Individu', 'Risqi Firdiansyah', 'Makan nasi telor + terong 03/10/2025', '2025-10-03', '2025-10-06', NULL, '2025-10-03 06:23:47', '2025-10-03 06:23:47', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `debt_request`
--

CREATE TABLE `debt_request` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `debtor_user_id` bigint(20) UNSIGNED NOT NULL,
  `creditor_user_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_hutang` varchar(255) NOT NULL DEFAULT 'Kontrak',
  `keterangan` varchar(255) NOT NULL,
  `amount` double(8,2) NOT NULL,
  `status` enum('Pending','Diterima (Belum Bayar)','Ditolak','Lunas','Pembayaran Diajukan') NOT NULL DEFAULT 'Pending',
  `debt_date` date NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `money_placing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `debt_request`
--

INSERT INTO `debt_request` (`id`, `debtor_user_id`, `creditor_user_id`, `jenis_hutang`, `keterangan`, `amount`, `status`, `debt_date`, `due_date`, `created_at`, `updated_at`, `money_placing_id`) VALUES
(16, 3, 1, 'Kontrak', 'Makan + minum SWK', 18000.00, 'Pembayaran Diajukan', '2025-09-09', '2025-09-13', '2025-09-09 02:25:21', '2025-09-10 20:10:19', 11),
(25, 3, 2, 'Kontrak', 'harusnya cas jadi 70k\n', 5000.00, 'Diterima (Belum Bayar)', '2025-09-13', '2025-09-19', '2025-09-13 00:24:38', '2025-09-13 00:38:06', 11),
(26, 3, 2, 'Kontrak', '100k cash', 30000.00, 'Pembayaran Diajukan', '2025-09-13', '2025-09-17', '2025-09-13 01:19:24', '2025-09-13 01:39:17', 11);

-- --------------------------------------------------------

--
-- Struktur dari tabel `debt_request_payment`
--

CREATE TABLE `debt_request_payment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `debt_request_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('Pembayaran Diajukan','Lunas') NOT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `money_placing_save` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `receipt_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `debt_request_payment`
--

INSERT INTO `debt_request_payment` (`id`, `debt_request_id`, `status`, `bukti_bayar`, `money_placing_save`, `created_at`, `updated_at`, `payment_date`, `receipt_date`) VALUES
(1, 16, 'Lunas', 'Bukti-bayar/3-User Hutang/f7e560d4-cf6b-4678-a0d5-217832d6eb5e/no-catatan.jpg', 1, '2025-09-10 20:10:19', '2025-09-12 00:31:59', '2025-09-11', '2025-09-12'),
(2, 26, 'Lunas', 'Bukti-bayar/3-User Hutang/2f4aa4ea-adb9-4053-8329-9f2c886262ce/no-catatan.jpg', 8, '2025-09-13 01:39:17', '2025-09-13 02:04:03', '2025-09-13', '2025-09-13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `financial_plans`
--

CREATE TABLE `financial_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `target_amount` decimal(15,2) NOT NULL,
  `amount_now` decimal(15,2) NOT NULL DEFAULT 0.00,
  `target_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `financial_plans`
--

INSERT INTO `financial_plans` (`id`, `user_id`, `description`, `target_amount`, `amount_now`, `target_date`, `created_at`, `updated_at`) VALUES
(4, 1, 'Beli cincin perak untuk na', 1500000.00, 300000.00, '2026-01-31', '2025-07-13 08:27:05', '2025-10-02 05:56:49'),
(5, 1, 'Ibadah Haji', 40000000.00, 10100000.00, '2025-08-06', '2025-08-06 00:40:39', '2025-08-06 00:43:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `financial_plan_progress`
--

CREATE TABLE `financial_plan_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_financial_plan` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `presentase_progress` decimal(15,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `financial_plan_progress`
--

INSERT INTO `financial_plan_progress` (`id`, `id_financial_plan`, `amount`, `presentase_progress`, `date`, `created_at`, `updated_at`) VALUES
(16, 4, 200000.00, 13.33, '2025-07-20', '2025-07-20 03:20:14', '2025-07-20 03:20:14'),
(17, 5, 100000.00, 0.25, '2025-08-06', '2025-08-06 00:41:41', '2025-08-06 00:42:39'),
(18, 5, 10000000.00, 25.00, '2025-08-07', '2025-08-06 00:43:35', '2025-08-06 00:43:35'),
(20, 4, 100000.00, 6.67, '2025-10-02', '2025-10-02 05:56:49', '2025-10-02 05:56:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '1.2014_10_12_000000_create_users_table', 1),
(2, '2.2025_07_12_123701_add_table_category', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2025_07_06_171739_transaction', 1),
(5, '2025_07_06_172054_add_new_table_financial_plans', 1),
(6, '2025_07_12_140125_make_table_financial_pan_progress', 1),
(7, '2025_07_12_140147_make_table_debt_record', 1),
(8, '2025_07_12_153319_add_login_tracking_fields_to_users_table', 2),
(9, '2025_07_13_140021_create_money_placing_table', 3),
(10, '2025_07_13_140027_add_money_placing_id_to_transactions_table', 3),
(11, '2025_07_13_144022_add_money_placing_id_to_transactions_table', 4),
(12, '2025_07_27_113743_create_table_monthly_plan', 5),
(13, '2025_07_27_115030_new_record_last_login_and_status_on_users', 5),
(14, '2025_07_30_152534_add_column_year_and_month', 6),
(15, '2025_08_10_021915_create_remember_tokens_table', 7),
(16, '2025_08_10_024219_drop_status_from_users_table', 8),
(17, '2025_08_10_024326_create_column_remember_tokens_users', 9),
(18, '2025_08_10_024349_create_column_status_users', 9),
(19, '2025_08_10_024423_create_column_remember_token_users', 9),
(20, '2025_08_10_084248_create_new_column_status_and__jenis_hutang_at_debt_records_table', 10),
(21, '2025_08_10_091503_create_new_column_id_user_at_debt_records_table', 11),
(22, '2025_08_12_144637_create_table_debt_request_', 12),
(23, '2025_08_13_144043_create_new_column_id_debt_on_tables_debt_request', 13),
(24, '2025_09_01_130508_create_column_money_placing_id_at_debt_record', 14),
(25, '2025_09_05_132203_drop_id_debt_from_some_table', 15),
(26, '2025_09_05_135049_add_more_column_at_table_debt_request', 16),
(27, '2025_09_09_062213_create_new_column_money_placing_id_on_debt_requst_table', 17),
(28, '2025_09_10_054938_create_table_pembayaran_hutang_kontrak', 18),
(29, '2025_09_10_062728_create_table_pembayaran_hutang_kontrak', 19),
(30, '2025_09_11_123333_create_new_column_payment_date_on_debt_request_payment_table', 20),
(31, '2025_09_12_072420_create_new_column_receopt_date_on_table_debt_request_payment', 21);

-- --------------------------------------------------------

--
-- Struktur dari tabel `money_placing`
--

CREATE TABLE `money_placing` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `money_placing`
--

INSERT INTO `money_placing` (`id`, `user_id`, `name`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 'Cash', 550000.00, '2025-07-13 07:08:53', '2025-10-03 23:25:19'),
(2, 1, 'M-Bank BSI', 350219.00, '2025-07-13 07:32:52', '2025-10-03 23:22:24'),
(8, 2, 'Dompet 1', 205000.00, '2025-09-01 23:55:06', '2025-09-14 09:06:19'),
(9, 2, 'Dompet 2 ', 996000.00, '2025-09-01 23:55:28', '2025-09-12 23:49:13'),
(10, 2, 'BCA', 86670000.00, '2025-09-01 23:55:51', '2025-09-02 06:22:06'),
(11, 3, 'Cash', 70000.00, '2025-09-08 23:53:02', '2025-09-13 01:39:17'),
(12, 3, 'Dana', 440000.00, '2025-09-08 23:53:26', '2025-09-10 20:10:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `monthly_plan`
--

CREATE TABLE `monthly_plan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `max_amount` decimal(15,2) NOT NULL,
  `amount_now` decimal(15,2) NOT NULL DEFAULT 0.00,
  `year` int(11) DEFAULT NULL,
  `month` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `monthly_plan`
--

INSERT INTO `monthly_plan` (`id`, `user_id`, `category_id`, `name`, `description`, `max_amount`, `amount_now`, `year`, `month`, `created_at`, `updated_at`) VALUES
(9, 1, 2, 'Bensin Agustus 2025', 'Satu minggu 60.000', 240000.00, 0.00, 2025, 'Agustus', '2025-08-03 08:01:42', '2025-08-13 21:10:32'),
(10, 1, 7, 'Nongkrong (Cafe, Nugas, Dll) Agustus 2025', 'Satu kali transaksi max Rp.50.000', 150000.00, 0.00, 2025, 'Agustus', '2025-08-03 08:02:07', '2025-08-03 23:32:19'),
(11, 1, 7, 'Nongkrong (Cafe, Nugas, Dll) Juni 2025', 'Test', 300000.00, 0.00, 2025, 'Juni', '2025-08-04 05:31:24', '2025-08-03 22:32:14'),
(12, 1, 2, 'Bensin Juli 2025', 'Test', 300000.00, 40000.00, 2025, 'Juli', '2025-07-09 05:35:10', '2025-08-03 22:35:37'),
(13, 1, 2, 'Bensin September 2025', 'Rp 60.000 x 4 minggu = 240.000 + darurat Rp 30l.000', 270000.00, 60000.00, 2025, 'September', '2025-09-04 05:46:17', '2025-09-09 04:46:36'),
(14, 1, 4, 'Makan September 2025', 'Perhari max Rp. 30.000 ', 900000.00, 102000.00, 2025, 'September', '2025-09-04 05:47:20', '2025-09-08 02:08:29'),
(15, 1, 2, 'Bensin Oktober 2025', 'Bensin dibeli 4x dalam 1 bulan dengan transaksi Rp 60.000 - Rp 70.000', 280000.00, 0.00, 2025, 'Oktober', '2025-10-03 23:30:02', '2025-10-03 23:30:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('pemasukan','pengeluaran','hutang') NOT NULL,
  `categories_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `date` date NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `money_placing_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `categories_id`, `amount`, `date`, `note`, `created_at`, `updated_at`, `money_placing_id`) VALUES
(54, 2, 'pengeluaran', 9, 300000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 300.000', '2025-09-02 01:34:39', '2025-09-02 01:34:39', 10),
(55, 2, 'pengeluaran', 9, 300000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 300.000', '2025-09-02 01:36:08', '2025-09-02 01:36:08', 10),
(56, 2, 'pengeluaran', 9, 300000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 300.000', '2025-09-02 01:37:35', '2025-09-02 01:37:35', 10),
(57, 2, 'pengeluaran', 9, 300000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 300.000', '2025-09-02 01:38:40', '2025-09-02 01:38:40', 10),
(58, 2, 'pengeluaran', 9, 300000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 300.000', '2025-09-02 01:39:28', '2025-09-02 01:39:28', 10),
(59, 2, 'pengeluaran', 9, 300000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 300.000', '2025-09-02 01:40:44', '2025-09-02 01:40:44', 10),
(61, 2, 'pengeluaran', 9, 400000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 400.000', '2025-09-02 01:44:08', '2025-09-02 01:44:08', 10),
(64, 2, 'pengeluaran', 9, 500000.00, '2025-09-02', 'Pemberian hutang kepada pengguna 1 sebesar Rp 500.000', '2025-09-02 02:18:29', '2025-09-02 02:18:29', 10),
(66, 2, 'hutang', 8, 30000.00, '2025-09-02', 'Penerimaan hutang dari admin sebesar Rp 30000', '2025-09-02 03:24:07', '2025-09-02 03:24:07', 8),
(70, 2, 'hutang', 8, 30000.00, '2025-09-02', 'Penerimaan hutang dari pengguna 1 sebesar Rp 30.000', '2025-09-02 06:18:11', '2025-09-02 06:18:11', 8),
(71, 2, 'hutang', 8, 35000.00, '2025-09-02', 'Penerimaan hutang dari admin sebesar Rp 35000', '2025-09-02 06:21:08', '2025-09-02 06:21:08', 10),
(73, 2, 'hutang', 8, 35000.00, '2025-09-02', 'Penerimaan hutang dari pengguna 1 sebesar Rp 35.000', '2025-09-02 06:22:06', '2025-09-02 06:22:06', 10),
(92, 2, 'pengeluaran', 9, 10000.00, '2025-09-08', 'Pemberian hutang kepada pengguna 1 sebesar Rp 10.000', '2025-09-07 22:55:18', '2025-09-07 22:55:18', 8),
(93, 2, 'pengeluaran', 9, 10000.00, '2025-09-08', 'Pemberian hutang kepada pengguna 1 sebesar Rp 10.000', '2025-09-07 22:56:32', '2025-09-07 22:56:32', 8),
(94, 2, 'pengeluaran', 9, 10000.00, '2025-09-08', 'Pemberian hutang kepada pengguna 1 sebesar Rp 10.000', '2025-09-07 22:58:50', '2025-09-07 22:58:50', 8),
(101, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 00:29:46', '2025-09-09 00:29:46', 12),
(102, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 00:36:40', '2025-09-09 00:36:40', 12),
(103, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 00:38:21', '2025-09-09 00:38:21', 12),
(104, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 00:41:32', '2025-09-09 00:41:32', 12),
(105, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 00:44:46', '2025-09-09 00:44:46', 12),
(106, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 00:47:11', '2025-09-09 00:47:11', 12),
(107, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 00:52:34', '2025-09-09 00:52:34', 12),
(108, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 01:15:24', '2025-09-09 01:15:24', 12),
(109, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 01:16:26', '2025-09-09 01:16:26', 12),
(110, 3, 'hutang', 8, 8000.00, '2025-09-09', 'Penerimaan hutang dari  sebesar Rp 8000. Dengan caatan hutangtes fitur', '2025-09-09 01:16:27', '2025-09-09 01:16:27', 1),
(111, 3, 'pengeluaran', 9, 8000.00, '2025-09-09', 'Pemberian hutang kepada User Hutang sebesar Rp 8.000', '2025-09-09 01:20:53', '2025-09-09 01:20:53', 12),
(116, 3, 'hutang', 8, 18000.00, '2025-09-09', 'Penerimaan hutang dari User Hutang sebesar Rp 18. dengan catatan : Makan + minum SWK000', '2025-09-09 04:28:36', '2025-09-09 04:28:36', 11),
(118, 3, 'hutang', 8, 18000.00, '2025-09-09', 'Penerimaan hutang dari User Hutang sebesar Rp 18. dengan catatan : Makan + minum SWK000', '2025-09-09 04:32:15', '2025-09-09 04:32:15', 11),
(120, 3, 'pengeluaran', 10, 18000.00, '2025-09-11', 'Pembayaran hutang kepada User Hutang sebesar Rp 18.000 dengan keterangan Makan + minum SWK', '2025-09-10 18:44:14', '2025-09-10 18:44:14', 11),
(121, 3, 'pengeluaran', 10, 18000.00, '2025-09-11', 'Pembayaran hutang kepada User Hutang sebesar Rp 18.000 dengan keterangan Makan + minum SWK', '2025-09-10 18:48:58', '2025-09-10 18:48:58', 11),
(122, 3, 'pengeluaran', 10, 18000.00, '2025-09-11', 'Pembayaran hutang kepada User Hutang sebesar Rp 20.500 dengan rincian pembayaran = 18.000 + 2.500. Dengan keterangan hutang Makan + minum SWK', '2025-09-10 20:03:46', '2025-09-10 20:03:46', 12),
(123, 3, 'pengeluaran', 10, 18000.00, '2025-09-11', 'Pembayaran hutang kepada User Hutang sebesar Rp 19.000 dengan rincian pembayaran = 18.000 + 1.000. Dengan keterangan hutang Makan + minum SWK', '2025-09-10 20:10:19', '2025-09-10 20:10:19', 12),
(125, 2, 'pengeluaran', 9, 20000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 20.000', '2025-09-12 23:08:03', '2025-09-12 23:08:03', 8),
(126, 3, 'pemasukan', 8, 20000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 20. dengan catatan : Tes Fitur000', '2025-09-12 23:08:03', '2025-09-12 23:08:03', 11),
(127, 2, 'pengeluaran', 9, 8000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 8.000', '2025-09-12 23:28:50', '2025-09-12 23:28:50', 8),
(128, 3, 'pemasukan', 8, 8000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 8. dengan catatan : tes000', '2025-09-12 23:28:50', '2025-09-12 23:28:50', 11),
(129, 2, 'pengeluaran', 9, 2000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 2.000', '2025-09-12 23:36:05', '2025-09-12 23:36:05', 8),
(130, 3, 'pemasukan', 8, 2000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 2.000 dengan catatan : Harusnya jadi 60k money placing cash', '2025-09-12 23:36:05', '2025-09-12 23:36:05', 11),
(131, 2, 'pengeluaran', 9, 1000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 1.000', '2025-09-12 23:39:06', '2025-09-12 23:39:06', 9),
(132, 3, 'pemasukan', 8, 1000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 1.000 dengan catatan : se', '2025-09-12 23:39:06', '2025-09-12 23:39:06', 11),
(133, 2, 'pengeluaran', 9, 1000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 1.000', '2025-09-12 23:42:02', '2025-09-12 23:42:02', 9),
(134, 3, 'pemasukan', 8, 1000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 1.000 dengan catatan : harusnya cahs jadi 65k', '2025-09-12 23:42:02', '2025-09-12 23:42:02', 11),
(135, 2, 'pengeluaran', 9, 2000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 2.000', '2025-09-12 23:49:13', '2025-09-12 23:49:13', 9),
(136, 3, 'pemasukan', 8, 2000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 2.000 dengan catatan : harusnya jadi 67k', '2025-09-12 23:49:13', '2025-09-12 23:49:13', 11),
(137, 2, 'pengeluaran', 9, 5000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 5.000', '2025-09-13 00:38:06', '2025-09-13 00:38:06', 8),
(138, 3, 'pemasukan', 8, 5000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 5.000 dengan catatan : harusnya cas jadi 70k\n', '2025-09-13 00:38:06', '2025-09-13 00:38:06', 11),
(139, 2, 'pengeluaran', 9, 30000.00, '2025-09-13', 'Pemberian hutang kepada pengguna 1 sebesar Rp 30.000', '2025-09-13 01:19:46', '2025-09-13 01:19:46', 8),
(140, 3, 'pemasukan', 8, 30000.00, '2025-09-13', 'Penerimaan hutang dari User Hutang sebesar Rp 30.000 dengan catatan : 100k cash', '2025-09-13 01:19:46', '2025-09-13 01:19:46', 11),
(141, 3, 'pengeluaran', 10, 30000.00, '2025-09-13', 'Pembayaran hutang kepada User Hutang sebesar Rp 30.000 dengan rincian pembayaran = 30.000 (hutang). Dengan keterangan hutang 100k cash', '2025-09-13 01:39:17', '2025-09-13 01:39:17', 11),
(142, 2, 'pemasukan', 11, 30000.00, '2025-09-13', 'Hutang telah dibayar oleh User Hutang sebesar Rp 0. Dengan keterangan hutang ', '2025-09-13 02:04:03', '2025-09-13 02:04:03', 8),
(143, 2, 'hutang', 8, 5000.00, '2025-09-14', 'Penerimaan hutang dari Bagus sebesar Rp 5000', '2025-09-14 09:04:10', '2025-09-14 09:04:10', 8),
(144, 2, 'pemasukan', 8, 5000.00, '2025-09-14', 'Penerimaan hutang dari Budieh sebesar Rp 5000', '2025-09-14 09:06:19', '2025-09-14 09:06:19', 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `job` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','non-active') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `role`, `job`, `last_login`, `login_at`, `is_online`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$12$2b9xDoz6quh2UOINiDB9q.9otb2Yhjt9/3elb25Xv4BuxvoiR9/bW', '8QajC3ofopbHxlPMFZDO0ra8l4PVeeKhIPeDEWSoFOqEE9kNpGxKXTX1xDmc', 'admin', 'Mahasiswa', '2025-07-30 06:03:34', '2025-07-30 06:03:34', 1, 'active', '2025-07-12 07:39:11', '2025-09-08 07:01:41'),
(2, 'pengguna 1', 'pengguna1@gmail.com', '$2y$10$RhvELbMo.uqfhpvz/9mgXeUfLGAqbMFGAYtcEfcsVPZyLUvcqyVSi', '91Wt2KnNmICXfiOKxZeHKtGsAZ6Rh8Miu2YBRw8JyQRzODf97rJbQYisDcru', 'user', 'Mahasiswa', NULL, NULL, 0, 'active', '2025-07-12 08:07:18', '2025-07-12 08:07:18'),
(3, 'User Hutang', 'pengguna2@gmail.com', '$2y$12$kXaDRFch3BC2hSDUxc8EPeO65ws84XuBognnBVs40nCb97W/kCP/W', NULL, 'user', 'Mhs', NULL, NULL, 0, 'active', '2025-08-17 08:37:27', '2025-08-17 08:37:27');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `debt_records`
--
ALTER TABLE `debt_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debt_records_user_id_foreign` (`user_id`),
  ADD KEY `debt_records_money_placing_id_foreign` (`money_placing_id`);

--
-- Indeks untuk tabel `debt_request`
--
ALTER TABLE `debt_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debt_request_debtor_user_id_foreign` (`debtor_user_id`),
  ADD KEY `debt_request_creditor_user_id_foreign` (`creditor_user_id`),
  ADD KEY `debt_request_money_placing_id_foreign` (`money_placing_id`);

--
-- Indeks untuk tabel `debt_request_payment`
--
ALTER TABLE `debt_request_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_hutang_kontrak_debt_request_id_foreign` (`debt_request_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `financial_plans`
--
ALTER TABLE `financial_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_plans_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `financial_plan_progress`
--
ALTER TABLE `financial_plan_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_plan_progress_id_financial_plan_foreign` (`id_financial_plan`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `money_placing`
--
ALTER TABLE `money_placing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `money_placing_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `monthly_plan`
--
ALTER TABLE `monthly_plan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monthly_plan_user_id_foreign` (`user_id`),
  ADD KEY `monthly_plan_category_id_foreign` (`category_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_categories_id_foreign` (`categories_id`),
  ADD KEY `transactions_money_placing_id_foreign` (`money_placing_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_remember_token_unique` (`remember_token`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `debt_records`
--
ALTER TABLE `debt_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `debt_request`
--
ALTER TABLE `debt_request`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `debt_request_payment`
--
ALTER TABLE `debt_request_payment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `financial_plans`
--
ALTER TABLE `financial_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `financial_plan_progress`
--
ALTER TABLE `financial_plan_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `money_placing`
--
ALTER TABLE `money_placing`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `monthly_plan`
--
ALTER TABLE `monthly_plan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `debt_records`
--
ALTER TABLE `debt_records`
  ADD CONSTRAINT `debt_records_money_placing_id_foreign` FOREIGN KEY (`money_placing_id`) REFERENCES `money_placing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `debt_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `debt_request`
--
ALTER TABLE `debt_request`
  ADD CONSTRAINT `debt_request_creditor_user_id_foreign` FOREIGN KEY (`creditor_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `debt_request_debtor_user_id_foreign` FOREIGN KEY (`debtor_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `debt_request_money_placing_id_foreign` FOREIGN KEY (`money_placing_id`) REFERENCES `money_placing` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `debt_request_payment`
--
ALTER TABLE `debt_request_payment`
  ADD CONSTRAINT `pembayaran_hutang_kontrak_debt_request_id_foreign` FOREIGN KEY (`debt_request_id`) REFERENCES `debt_request` (`id`);

--
-- Ketidakleluasaan untuk tabel `financial_plans`
--
ALTER TABLE `financial_plans`
  ADD CONSTRAINT `financial_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `financial_plan_progress`
--
ALTER TABLE `financial_plan_progress`
  ADD CONSTRAINT `financial_plan_progress_id_financial_plan_foreign` FOREIGN KEY (`id_financial_plan`) REFERENCES `financial_plans` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `money_placing`
--
ALTER TABLE `money_placing`
  ADD CONSTRAINT `money_placing_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `monthly_plan`
--
ALTER TABLE `monthly_plan`
  ADD CONSTRAINT `monthly_plan_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monthly_plan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_categories_id_foreign` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_money_placing_id_foreign` FOREIGN KEY (`money_placing_id`) REFERENCES `money_placing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
