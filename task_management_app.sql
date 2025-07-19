-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 11:31 AM
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
-- Database: `task_management_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_contacts`
--

CREATE TABLE `tbl_contacts` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_contacts`
--

INSERT INTO `tbl_contacts` (`id`, `nama`, `email`, `pesan`, `created_at`) VALUES
(11, 'julia', 'juliacarenitareriola@gmail.com', 'hbjhjkk', '2025-07-15 16:14:53'),
(12, 'julia', 'juliacarenitareriola@gmail.com', 'wdnmnw', '2025-07-15 16:21:40'),
(13, 'julia', 'juliacarenitareriola@gmail.com', 'yuyyg', '2025-07-15 17:44:18'),
(14, 'julia', 'juliacarenitareriola@gmail.com', 'sdfbhjmf,sbfl', '2025-07-16 11:42:22'),
(15, 'julia', 'juliacarenitareriola@gmail.com', 'nsmdbhjsd', '2025-07-16 11:46:02'),
(16, 'julia', 'juliacarenitareriola@gmail.com', 'sjfgsjhgfjhskfhwjkfh', '2025-07-16 11:46:11'),
(17, 'carenita', 'carenitareri17@gmail.com', 'mantap', '2025-07-17 09:03:39'),
(18, 'julia', 'juliacarenitareriola@gmail.com', 'fs', '2025-07-17 09:35:10'),
(19, 'svvs', 'juliacarenitareriola@gmail.com', 'sjfgsjhgfjhskfhwjkfh', '2025-07-17 09:35:21');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_members`
--

CREATE TABLE `tbl_members` (
  `id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('manager','member') DEFAULT 'member',
  `joined_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_members`
--

INSERT INTO `tbl_members` (`id`, `team_id`, `name`, `photo`, `role`, `joined_at`) VALUES
(1, 10, 'Imelia Grasia', '6879cfe6bb457_IMG_7348.png', 'member', '2024-01-15'),
(2, 11, 'Sinta Ramadhani', '6879d01aad91a_photo-1618593167496-24fed8abacd3.jpg', 'manager', '2024-02-10'),
(4, 13, 'Budi Santoso', '6879eb5a5447a_premium_photo-1706429674214-0968.jpg', 'member', '2024-03-01'),
(5, 14, 'Rizky Pratama', '6879eb6b29cfb_photo-1714331251780-db56109a9887.jpg', 'manager', '2024-01-05'),
(6, 15, 'Andini Setiawan', '6879eb7eb5ae0_photo-1512503868941-bd9fa9c6b569.jpg', 'member', '2024-04-12'),
(7, 16, 'Dimas Arya', '6879f52131ddc_ft1.jpg', 'manager', '2023-11-30'),
(8, 17, 'Rahma Dewi', 'rahma.jpg', 'member', '2024-02-28'),
(12, 10, 'Frank Ocean', '6879fb5021bcf_ft1.jpg', 'member', '2025-07-18'),
(13, 18, 'july', '687af31d2bf75_logo_kkn-removebg-preview.png', 'member', '2025-07-23'),
(15, 10, 'july', '687b47476fef6_IMG_8505-removebg-preview.png', 'member', '2025-07-09');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_statuses`
--

CREATE TABLE `tbl_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_statuses`
--

INSERT INTO `tbl_statuses` (`id`, `name`, `created_at`) VALUES
(9, 'new', '2025-07-19 04:21:03'),
(10, 'todo', '2025-07-19 04:21:13'),
(11, 'complete', '2025-07-19 05:04:35'),
(12, 'in_review', '2025-07-19 10:27:56'),
(16, 'Selesai', '2025-07-19 11:41:10'),
(17, 'Reviewed', '2025-07-19 11:44:09'),
(18, 'Backlog', '2025-07-19 11:44:09'),
(19, 'Blocked', '2025-07-19 11:44:09'),
(21, 'Done', '2025-07-19 13:52:55'),
(22, 'Baru', '2025-07-19 14:02:39'),
(23, 'Penting', '2025-07-19 14:14:17');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tasks`
--

CREATE TABLE `tbl_tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `tag` varchar(50) DEFAULT NULL,
  `category` enum('tinggi','sedang','rendah') DEFAULT 'sedang',
  `team_id` int(11) DEFAULT NULL,
  `assignees` text DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_tasks`
--

INSERT INTO `tbl_tasks` (`id`, `title`, `description`, `created_at`, `deadline`, `tag`, `category`, `team_id`, `assignees`, `status_id`) VALUES
(48, 'Tugas 1', 'Deskripsi tugas 1', '2025-07-19', '2025-07-24', '1', 'tinggi', NULL, NULL, 10),
(49, 'Tugas 2', 'Deskripsi tugas ', '2025-07-19', '2025-07-25', 'Research Team', 'sedang', NULL, NULL, 9),
(50, 'Tugas 3', 'Deskripsi tugas 3', '2025-07-19', '2025-07-23', '3', 'rendah', NULL, NULL, 11),
(51, 'Tugas 4', 'Deskripsi tugas 4', '2025-07-19', '2025-07-22', '1', 'sedang', NULL, NULL, 12),
(52, 'Tugas 5', 'Deskripsi tugas 5.', '2025-07-19', '2025-07-21', 'Support Team', 'tinggi', NULL, NULL, 16),
(53, 'Tugas 6', 'Deskripsi tugas 6', '2025-07-19', '2025-07-26', '3', 'rendah', NULL, NULL, 17),
(54, 'Tugas 7', 'Deskripsi tugas 7', '2025-07-19', '2025-07-24', '1', 'tinggi', NULL, NULL, 18),
(55, 'Tugas 8', 'Deskripsi tugas 8', '2025-07-19', '2025-07-25', '2', 'sedang', NULL, NULL, 19),
(56, 'Tugas 9', 'Deskripsi tugas 9', '2025-07-19', '2025-07-23', '3', 'rendah', NULL, NULL, 18),
(58, 'Desain Landing Page', 'Tolong segera dikerjakan', '2025-07-19', '2025-08-06', 'Design Team', 'sedang', NULL, NULL, 21);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_teams`
--

CREATE TABLE `tbl_teams` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_teams`
--

INSERT INTO `tbl_teams` (`id`, `name`, `description`, `photo`, `created_at`) VALUES
(10, 'Developer Team', 'Bertanggung jawab atas pengembangan dan pemeliharaan semua aplikasi.', '6879b93e2ffd9_Desain tanpa judul (1).png', '2025-07-18'),
(11, 'Design Team', 'Fokus pada pembuatan UI/UX yang menarik dan responsif.', '6879b9b1e5c96_Desain tanpa judul (1).png', '2025-07-18'),
(12, 'Research Team', 'Melakukan analisis dan riset untuk peningkatan fitur dan teknologi terbaru.\'', NULL, '2025-07-18'),
(13, 'Marketing Team', 'Menyusun strategi pemasaran dan kampanye digital.', NULL, NULL),
(14, 'Support Team', 'Memberikan dukungan teknis dan bantuan kepada pengguna.', '687a6b55ba0d0_Desain tanpa judul (1).png', NULL),
(15, 'QA Team', 'Menjamin kualitas aplikasi melalui proses testing dan validasi.', NULL, NULL),
(16, 'HR Team', 'Mengelola kebutuhan rekrutmen dan kesejahteraan karyawan.', NULL, NULL),
(17, 'Finance Team', 'Bertanggung jawab atas pengelolaan keuangan dan anggaran proyek.', NULL, NULL),
(18, 'Content Team', 'Membuat dan mengelola konten untuk media sosial dan situs web.', NULL, NULL),
(19, 'Legal Team', 'Memastikan seluruh aktivitas perusahaan sesuai dengan hukum dan regulasi.', NULL, NULL),
(20, 'Operation Team', 'Mengatur operasional harian agar dengan proyek berjalan lancar', NULL, NULL),
(22, 'Team bincang\"', 'bincang\" aja', '687a736d075e9_11zon_cropped.png', '2025-07-18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `first_name`, `username`, `password`, `created_at`) VALUES
(1, 'carenita', 'admin', '$2y$10$YBPi99uuEj5AlItxxVbV1eJl1w480/wHdzmB2I4YtP8eVKlXL36BS', '2025-07-10 16:31:43'),
(2, 'carenita', 'caren', '$2y$10$ZSdFWo3KPPhvoGmiQlAJH.Dn.3COQxF18o3oGHvZjOCU4hoYKaSyq', '2025-07-17 09:04:09'),
(3, 'Imelia', 'hilda', '$2y$10$ffTeMT8X/9XZmWoL80hR3O4ma/Gv/OmxffhRVvt2Eo4LwjxRsyTL.', '2025-07-19 05:20:15'),
(4, 'Imelia', 'grasia', '$2y$10$gaWgry6liLlXY3ya5HNH/O8ukrqPewmUEM4dVi8r4R2huPSLyYPiy', '2025-07-19 06:13:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_contacts`
--
ALTER TABLE `tbl_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_members`
--
ALTER TABLE `tbl_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_team_id` (`team_id`);

--
-- Indexes for table `tbl_statuses`
--
ALTER TABLE `tbl_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tbl_tasks`
--
ALTER TABLE `tbl_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_team` (`team_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `tbl_teams`
--
ALTER TABLE `tbl_teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_contacts`
--
ALTER TABLE `tbl_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_members`
--
ALTER TABLE `tbl_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_statuses`
--
ALTER TABLE `tbl_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tbl_tasks`
--
ALTER TABLE `tbl_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `tbl_teams`
--
ALTER TABLE `tbl_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_members`
--
ALTER TABLE `tbl_members`
  ADD CONSTRAINT `fk_team_id` FOREIGN KEY (`team_id`) REFERENCES `tbl_teams` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_tasks`
--
ALTER TABLE `tbl_tasks`
  ADD CONSTRAINT `fk_team` FOREIGN KEY (`team_id`) REFERENCES `tbl_teams` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_tasks_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `tbl_statuses` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
