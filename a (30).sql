-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 13 نوفمبر 2023 الساعة 11:15
-- إصدار الخادم: 8.0.31
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `a`
--

DELIMITER $$
--
-- الإجراءات
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `DropColumnIfExists` (IN `table_name` VARCHAR(255), IN `column_name` VARCHAR(255))   BEGIN
    DECLARE column_exists INT;

    SELECT COUNT(*) INTO column_exists
    FROM information_schema.columns
    WHERE table_name = table_name AND column_name = column_name;

    IF column_exists > 0 THEN
        SET @sql = CONCAT('ALTER TABLE ', table_name, ' DROP COLUMN ', column_name);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- بنية الجدول `clientdata`
--

CREATE TABLE `clientdata` (
  `ClientID` int NOT NULL,
  `ClientName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `NEWREADING` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `PreviousReading` decimal(10,2) DEFAULT NULL,
  `CurrentReading` decimal(10,2) DEFAULT NULL,
  `PricePerKilo` decimal(10,2) DEFAULT NULL,
  `SubscriptionPrice` decimal(10,2) DEFAULT NULL,
  `TotalAmount` decimal(10,2) GENERATED ALWAYS AS ((case when (`CurrentReading` > `PreviousReading`) then (((`CurrentReading` - `PreviousReading`) * `PricePerKilo`) + `SubscriptionPrice`) else `SubscriptionPrice` end)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `clientdata`
--

INSERT INTO `clientdata` (`ClientID`, `ClientName`, `NEWREADING`, `PreviousReading`, `CurrentReading`, `PricePerKilo`, `SubscriptionPrice`) VALUES
(1, '2', '40', 184.00, 185.00, 11.00, 11.00),
(2, 'صس', '10', 36.00, 37.00, 44.00, 11.00),
(123123, 'محمد احمد عمر صالح البدوي جديد ', '', 230.00, 10.00, 33.00, 33.00);

--
-- القوادح `clientdata`
--
DELIMITER $$
CREATE TRIGGER `CalculateTotalAmount` BEFORE UPDATE ON `clientdata` FOR EACH ROW BEGIN
    DECLARE existingTotal DECIMAL(10, 2) DEFAULT 0.00;

    SELECT COALESCE(SUM(p.PaymentAmount), 0) INTO existingTotal
    FROM total t
    LEFT JOIN payments p ON t.ClientID = p.ClientID
    WHERE t.ClientID = NEW.ClientID;

    SET NEW.TotalAmount = 
        CASE 
            WHEN NEW.CurrentReading > NEW.PreviousReading THEN 
                (((NEW.CurrentReading - NEW.PreviousReading) * NEW.PricePerKilo) + NEW.SubscriptionPrice) + existingTotal
            ELSE 
                NEW.SubscriptionPrice + existingTotal
        END;

    INSERT INTO Total (ClientID, TotalAmount) VALUES (NEW.ClientID, NEW.TotalAmount)
    ON DUPLICATE KEY UPDATE TotalAmount = NEW.TotalAmount;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_insert_clientdata` BEFORE INSERT ON `clientdata` FOR EACH ROW BEGIN
    -- حفظ القيمة الحالية لل NEWREADING
    SET @temp_newreading = NEW.NEWREADING;

    -- إعادة قيمة CurrentReading إلى 0
    SET NEW.CurrentReading = 0;

    -- نقل القيمة من NEWREADING إلى PreviousReading
    SET NEW.PreviousReading = @temp_newreading;

    -- إعادة قيمة NEWREADING إلى '00'
    SET NEW.NEWREADING = '00';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- بنية الجدول `detailedreport`
--

CREATE TABLE `detailedreport` (
  `ReportID` int NOT NULL,
  `ClientID` int NOT NULL,
  `ReportDate` date NOT NULL,
  `Details` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `detailedreport`
--

INSERT INTO `detailedreport` (`ReportID`, `ClientID`, `ReportDate`, `Details`) VALUES
(1, 1, '2023-11-12', 'Details for ClientID 1');

-- --------------------------------------------------------

--
-- بنية الجدول `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int NOT NULL,
  `ClientID` int NOT NULL,
  `PaymentAmount` decimal(10,2) NOT NULL,
  `PaymentDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `payments`
--

INSERT INTO `payments` (`PaymentID`, `ClientID`, `PaymentAmount`, `PaymentDateTime`) VALUES
(1, 1, 111.00, '2023-11-11 20:21:27'),
(2, 2, 111.00, '2023-11-12 11:05:43'),
(3, 1, 100.00, '2023-11-12 12:20:12'),
(4, 2, 1000.00, '2023-11-12 12:20:19'),
(5, 1, 111.00, '2023-11-12 12:20:25'),
(6, 2, 100.00, '2023-11-12 12:20:36'),
(7, 1, 111.00, '2023-11-12 13:22:55'),
(8, 1, 111.00, '2023-11-12 13:23:04'),
(9, 1, 22.00, '2023-11-12 13:47:46'),
(1212121215, 123123, 1.00, '2023-11-12 14:27:26'),
(1212121216, 123123, 220.00, '2023-11-12 14:39:03'),
(1212121217, 123123, 11.00, '2023-11-12 14:41:35'),
(1212121218, 123123, 220.00, '2023-11-12 14:49:27'),
(1212121219, 123123, 11.00, '2023-11-12 14:51:02'),
(1212121220, 123123, 99.00, '2023-11-12 14:52:16'),
(1212121221, 123123, 1.00, '2023-11-12 14:57:50'),
(1212121222, 123123, 11.00, '2023-11-12 14:59:57'),
(1212121223, 123123, 1.00, '2023-11-12 15:00:09'),
(1212121224, 123123, 2.00, '2023-11-12 15:03:10'),
(1212121225, 123123, 1.00, '2023-11-12 15:04:28'),
(1212121226, 123123, 1.00, '2023-11-12 15:05:59'),
(1212121227, 123123, 1.00, '2023-11-12 15:09:49'),
(1212121228, 123123, 1.00, '2023-11-12 15:22:59'),
(1212121229, 2, 10.00, '2023-11-12 15:46:19'),
(1212121230, 123123, 2.00, '2023-11-12 15:50:23');

--
-- القوادح `payments`
--
DELIMITER $$
CREATE TRIGGER `UpdateRemainingAmount` AFTER INSERT ON `payments` FOR EACH ROW BEGIN
    DECLARE totalPayments DECIMAL(10, 2);
    DECLARE remainingAmount DECIMAL(10, 2);

    SELECT COALESCE(SUM(p.PaymentAmount), 0) INTO totalPayments
    FROM payments p
    WHERE p.ClientID = NEW.ClientID;

    SET remainingAmount = (SELECT TotalAmount FROM total WHERE ClientID = NEW.ClientID) - totalPayments;

    INSERT INTO remainingamount (ClientID, TotalPayments, RemainingAmount) 
    VALUES (NEW.ClientID, totalPayments, remainingAmount) 
    ON DUPLICATE KEY UPDATE TotalPayments = totalPayments, RemainingAmount = remainingAmount;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- بنية الجدول `remainingamount`
--

CREATE TABLE `remainingamount` (
  `ClientID` int NOT NULL,
  `TotalPayments` decimal(10,2) DEFAULT NULL,
  `RemainingAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `remainingamount`
--

INSERT INTO `remainingamount` (`ClientID`, `TotalPayments`, `RemainingAmount`) VALUES
(1, 566.00, -434.00),
(2, 1221.00, -736.00),
(123123, 583.00, 174.00);

-- --------------------------------------------------------

--
-- بنية الجدول `summaryreport`
--

CREATE TABLE `summaryreport` (
  `SummaryReportID` int NOT NULL,
  `ReportDate` date NOT NULL,
  `TotalClients` int NOT NULL,
  `TotalPayments` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `summaryreport`
--

INSERT INTO `summaryreport` (`SummaryReportID`, `ReportDate`, `TotalClients`, `TotalPayments`) VALUES
(1, '2023-11-12', 9, 1777.00);

-- --------------------------------------------------------

--
-- بنية الجدول `total`
--

CREATE TABLE `total` (
  `ClientID` int NOT NULL,
  `TotalAmount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `total`
--

INSERT INTO `total` (`ClientID`, `TotalAmount`) VALUES
(1, 588.00),
(2, 1276.00),
(123123, 616.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clientdata`
--
ALTER TABLE `clientdata`
  ADD PRIMARY KEY (`ClientID`),
  ADD KEY `idx_TotalAmount` (`TotalAmount`);

--
-- Indexes for table `detailedreport`
--
ALTER TABLE `detailedreport`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `fk_detailedreport_clientdata` (`ClientID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `fk_payments_clientdata` (`ClientID`);

--
-- Indexes for table `remainingamount`
--
ALTER TABLE `remainingamount`
  ADD PRIMARY KEY (`ClientID`);

--
-- Indexes for table `summaryreport`
--
ALTER TABLE `summaryreport`
  ADD PRIMARY KEY (`SummaryReportID`);

--
-- Indexes for table `total`
--
ALTER TABLE `total`
  ADD PRIMARY KEY (`ClientID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detailedreport`
--
ALTER TABLE `detailedreport`
  MODIFY `ReportID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1212121231;

--
-- AUTO_INCREMENT for table `summaryreport`
--
ALTER TABLE `summaryreport`
  MODIFY `SummaryReportID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `detailedreport`
--
ALTER TABLE `detailedreport`
  ADD CONSTRAINT `fk_detailedreport_clientdata` FOREIGN KEY (`ClientID`) REFERENCES `clientdata` (`ClientID`) ON DELETE CASCADE;

--
-- قيود الجداول `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_clientdata` FOREIGN KEY (`ClientID`) REFERENCES `clientdata` (`ClientID`);

--
-- قيود الجداول `remainingamount`
--
ALTER TABLE `remainingamount`
  ADD CONSTRAINT `fk_remainingamount_total` FOREIGN KEY (`ClientID`) REFERENCES `total` (`ClientID`);

--
-- قيود الجداول `total`
--
ALTER TABLE `total`
  ADD CONSTRAINT `fk_total_clientdata` FOREIGN KEY (`ClientID`) REFERENCES `clientdata` (`ClientID`);

DELIMITER $$
--
-- أحداث
--
CREATE DEFINER=`root`@`localhost` EVENT `UpdateSummaryReport` ON SCHEDULE EVERY 1 DAY STARTS '2023-11-12 17:01:39' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    INSERT INTO `summaryreport` (`ReportDate`, `TotalClients`, `TotalPayments`)
    SELECT 
        CURDATE() AS `ReportDate`,
        COUNT(`clientdata`.`ClientID`) AS `TotalClients`,
        COALESCE(SUM(`payments`.`PaymentAmount`), 0) AS `TotalPayments`
    FROM `clientdata`
    LEFT JOIN `payments` ON `clientdata`.`ClientID` = `payments`.`ClientID`;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
