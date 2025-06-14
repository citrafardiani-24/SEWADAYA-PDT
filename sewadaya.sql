-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 14, 2025 at 03:05 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sewadaya`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CreateRental` (IN `p_user_id` INT, IN `p_item_id` INT, IN `p_quantity` INT, IN `p_rental_date` DATE, IN `p_return_date` DATE, IN `p_rental_price_per_day` DECIMAL(10,2))   BEGIN
    DECLARE v_days INT;
    DECLARE v_total_price DECIMAL(10, 2);

    -- Hitung jumlah hari penyewaan. Jika kurang dari 1 hari, anggap 1 hari.
    SET v_days = DATEDIFF(p_return_date, p_rental_date);
    IF v_days < 1 THEN
        SET v_days = 1;
    END IF;

    -- Hitung total harga penyewaan (harga_per_hari * hari * kuantitas)
    SET v_total_price = v_days * p_rental_price_per_day * p_quantity;

    START TRANSACTION; -- Memulai transaksi
    BEGIN
        -- Kurangi stok barang sesuai kuantitas. Hanya jika stok mencukupi.
        UPDATE clothing_items
        SET stock = stock - p_quantity
        WHERE item_id = p_item_id AND stock >= p_quantity; -- Pastikan stok mencukupi

        -- Jika tidak ada baris yang diperbarui (stok kosong atau tidak mencukupi, atau item tidak ditemukan), rollback transaksi
        IF ROW_COUNT() = 0 THEN
            ROLLBACK;
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Barang tidak tersedia atau stok tidak mencukupi.';
        END IF;

        -- Masukkan catatan penyewaan ke tabel 'rentals'
        INSERT INTO rentals (user_id, item_id, quantity, rental_date, return_date, total_price, status)
        VALUES (p_user_id, p_item_id, p_quantity, p_rental_date, p_return_date, v_total_price, 'pending');

        COMMIT; -- Mengakhiri transaksi dengan komit jika semua operasi berhasil
    END;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ReturnRental` (IN `p_rental_id` INT, IN `p_actual_return_date` DATE)   BEGIN
    DECLARE v_item_id INT;
    DECLARE v_rental_status ENUM('pending', 'rented', 'returned', 'cancelled');

    -- Dapatkan ID item dan status penyewaan saat ini dari rental_id
    SELECT item_id, status INTO v_item_id, v_rental_status
    FROM rentals
    WHERE rental_id = p_rental_id;

    -- Lanjutkan jika rental_id ditemukan dan status belum 'returned'
    IF v_item_id IS NOT NULL AND v_rental_status <> 'returned' THEN
        START TRANSACTION; -- Memulai transaksi
        BEGIN
            -- Perbarui status penyewaan dan tanggal pengembalian aktual
            UPDATE rentals
            SET status = 'returned', actual_return_date = p_actual_return_date
            WHERE rental_id = p_rental_id;

            -- Tambah stok barang kembali ke tabel 'clothing_items'
            UPDATE clothing_items
            SET stock = stock + 1
            WHERE item_id = v_item_id;

            COMMIT; -- Mengakhiri transaksi dengan komit
        END;
    ELSEIF v_item_id IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ID penyewaan tidak ditemukan.';
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Penyewaan sudah dikembalikan.';
    END IF;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalculateOverdueDays` (`p_rental_id` INT) RETURNS INT READS SQL DATA BEGIN
    DECLARE v_return_date DATE;
    DECLARE v_actual_return_date DATE;
    DECLARE overdue_days INT;

    -- Dapatkan tanggal pengembalian yang diharapkan dan aktual dari tabel rentals
    SELECT return_date, actual_return_date INTO v_return_date, v_actual_return_date
    FROM rentals
    WHERE rental_id = p_rental_id;

    -- Logika perhitungan hari keterlambatan
    IF v_actual_return_date IS NOT NULL AND v_actual_return_date > v_return_date THEN
        SET overdue_days = DATEDIFF(v_actual_return_date, v_return_date);
    ELSEIF v_actual_return_date IS NULL AND CURRENT_DATE() > v_return_date THEN
        SET overdue_days = DATEDIFF(CURRENT_DATE(), v_return_date);
    ELSE
        SET overdue_days = 0;
    END IF;

    RETURN overdue_days;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `GetUserTotalRentals` (`p_user_id` INT) RETURNS INT READS SQL DATA BEGIN
    DECLARE total_rentals INT;
    SELECT COUNT(*) INTO total_rentals FROM rentals WHERE user_id = p_user_id;
    RETURN total_rentals;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `clothing_items`
--

CREATE TABLE `clothing_items` (
  `item_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `size` enum('anak-anak','dewasa') NOT NULL,
  `rental_price_per_day` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `clothing_items`
--

INSERT INTO `clothing_items` (`item_id`, `name`, `description`, `size`, `rental_price_per_day`, `stock`, `image_url`, `created_at`) VALUES
(5, 'baju kurung', 'pakaian adat lampung', 'dewasa', '50000.00', 4, 'uploads/img_684c6e6a717a3.jpg', '2025-06-13 18:31:06'),
(9, 'king baba', 'baju adat dayak untuk laki laki', 'dewasa', '300000.00', 4, 'uploads/img_684cc3878878b.jpg', '2025-06-14 00:34:15'),
(11, 'baju adat bali', 'buat cowo', 'anak-anak', '10000.00', 5, 'uploads/img_684cd7bda2cca.jpg', '2025-06-14 02:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int NOT NULL,
  `rental_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `rental_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','rented','returned','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`rental_id`, `user_id`, `item_id`, `quantity`, `rental_date`, `return_date`, `actual_return_date`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 5, 1, '2025-06-13', '2025-06-14', NULL, '50000.00', 'cancelled', '2025-06-13 23:48:42'),
(3, 1, 5, 1, '2025-06-15', '2025-06-16', NULL, '50000.00', 'cancelled', '2025-06-13 23:50:13'),
(5, 1, 5, 1, '2025-06-16', '2025-06-18', NULL, '100000.00', 'cancelled', '2025-06-13 23:51:10'),
(7, 1, 5, 1, '2025-06-18', '2025-06-20', NULL, '100000.00', 'cancelled', '2025-06-13 23:57:34'),
(9, 1, 5, 1, '2025-06-23', '2025-06-25', '2025-06-14', '100000.00', 'returned', '2025-06-14 00:00:20'),
(11, 1, 5, 1, '2025-06-15', '2025-06-17', '2025-06-14', '100000.00', 'returned', '2025-06-14 00:27:19'),
(13, 7, 9, 1, '2025-06-14', '2025-06-15', '2025-06-14', '300000.00', 'returned', '2025-06-14 00:35:12'),
(15, 7, 9, 1, '2025-06-14', '2025-06-15', NULL, '300000.00', 'rented', '2025-06-14 00:36:22'),
(17, 7, 5, 1, '2025-06-14', '2025-06-18', NULL, '200000.00', 'rented', '2025-06-14 01:20:21'),
(19, 7, 5, 5, '2025-06-14', '2025-06-15', NULL, '50000.00', 'rented', '2025-06-14 01:44:44'),
(21, 1, 11, 5, '2025-06-14', '2025-06-16', NULL, '20000.00', 'pending', '2025-06-14 02:03:18');

--
-- Triggers `rentals`
--
DELIMITER $$
CREATE TRIGGER `AfterUpdateRentalStatus` AFTER UPDATE ON `rentals` FOR EACH ROW BEGIN
    -- Jika status lama berbeda dengan status baru, masukkan catatan log
    IF OLD.status <> NEW.status THEN
        INSERT INTO rental_status_log (rental_id, old_status, new_status)
        VALUES (OLD.rental_id, OLD.status, NEW.status);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `BeforeInsertRental` BEFORE INSERT ON `rentals` FOR EACH ROW BEGIN
    DECLARE v_rental_price_per_day DECIMAL(10, 2);
    DECLARE v_days INT;

    -- Dapatkan harga sewa per hari dari item yang terkait
    SELECT rental_price_per_day INTO v_rental_price_per_day
    FROM clothing_items
    WHERE item_id = NEW.item_id;

    -- Hitung jumlah hari penyewaan. Jika kurang dari 1 hari, anggap 1 hari.
    SET v_days = DATEDIFF(NEW.return_date, NEW.rental_date);
    IF v_days < 1 THEN
        SET v_days = 1;
    END IF;

    -- Set total_price untuk baris yang akan dimasukkan
    SET NEW.total_price = v_days * v_rental_price_per_day;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rental_status_log`
--

CREATE TABLE `rental_status_log` (
  `log_id` int NOT NULL,
  `rental_id` int DEFAULT NULL,
  `old_status` enum('pending','rented','returned','cancelled') DEFAULT NULL,
  `new_status` enum('pending','rented','returned','cancelled') DEFAULT NULL,
  `change_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rental_status_log`
--

INSERT INTO `rental_status_log` (`log_id`, `rental_id`, `old_status`, `new_status`, `change_date`) VALUES
(1, 1, 'pending', 'cancelled', '2025-06-13 23:49:23'),
(3, 3, 'pending', 'cancelled', '2025-06-13 23:50:35'),
(5, 5, 'pending', 'cancelled', '2025-06-13 23:51:36'),
(7, 7, 'pending', 'cancelled', '2025-06-13 23:58:15'),
(9, 9, 'pending', 'rented', '2025-06-14 00:01:14'),
(11, 9, 'rented', 'returned', '2025-06-14 00:01:29'),
(13, 11, 'pending', 'rented', '2025-06-14 00:29:01'),
(15, 17, 'pending', 'rented', '2025-06-14 01:38:40'),
(17, 13, 'pending', 'rented', '2025-06-14 01:38:44'),
(19, 15, 'pending', 'rented', '2025-06-14 01:38:46'),
(21, 19, 'pending', 'rented', '2025-06-14 02:02:12'),
(23, 11, 'rented', 'returned', '2025-06-14 02:02:18'),
(25, 13, 'rented', 'returned', '2025-06-14 02:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'maw', '$2y$10$dNwWeXawiX1sckqXoQL0g.Pw4HD3MQsXpznhWX.l43hzfL/LSGp0m', 'maw@gmail.com', 'customer', '2025-06-13 16:55:51'),
(3, 'admin', '$2y$10$no11HKYJgbyuzw8SCh5ogeWxdRkyqAgpuPVA6BLCN30J1KXUNDsgy', 'admin@example.com', 'admin', '2025-06-13 17:39:44'),
(7, 'citra', '$2y$10$hECV4fZzv4lxeccYJ63vse91qiECdYsYJViSy0X3myW0veE7a.YvK', 'citrafardiani@gmail.com', 'customer', '2025-06-13 22:50:25'),
(9, 'adin', '$2y$10$smJkqILucTLrO8eMoq94qOlLIOlE46Z/xhkGSpc/aeG4FFZgoregq', 'adin@gmail.com', 'customer', '2025-06-14 02:01:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clothing_items`
--
ALTER TABLE `clothing_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `rental_id` (`rental_id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `rental_status_log`
--
ALTER TABLE `rental_status_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clothing_items`
--
ALTER TABLE `clothing_items`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `rental_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `rental_status_log`
--
ALTER TABLE `rental_status_log`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`rental_id`) REFERENCES `rentals` (`rental_id`);

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `clothing_items` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
