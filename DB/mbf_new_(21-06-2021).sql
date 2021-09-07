-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.11-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table mollabricks-accounting_new.accounting_head
CREATE TABLE IF NOT EXISTS `accounting_head` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(55) NOT NULL,
  `name` varchar(55) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `head_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1:Expances, 2: ncome, 3:Liabilities ',
  `project_id` int(11) DEFAULT NULL,
  `flat_id` int(11) DEFAULT NULL,
  `account_no` int(11) NOT NULL,
  `editable` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0: no; 1:yes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `id` (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `FK1_project_id` FOREIGN KEY (`project_id`) REFERENCES `project_infos` (`project_code`)
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.accounting_head: ~3 rows (approximately)
/*!40000 ALTER TABLE `accounting_head` DISABLE KEYS */;
INSERT INTO `accounting_head` (`id`, `code`, `name`, `parent`, `head_type`, `project_id`, `flat_id`, `account_no`, `editable`) VALUES
	(1, 'IN00000001', 'Deposit', NULL, 2, NULL, NULL, 1, 0),
	(2, 'EX00000001', 'Withdraw', NULL, 1, NULL, NULL, 1, 0),
	(5, 'LI00000001', 'Liability', NULL, 3, NULL, NULL, 1, 0),
	(100, 'IN00000002', 'Brick Sale', NULL, 2, NULL, NULL, 1, 1),
	(101, 'IN00000003', '1No Bangla Brick Sale', 100, 2, NULL, NULL, 1, 1),
	(102, 'IN00000004', '2No Bangla Brick Sale', 100, 2, NULL, NULL, 1, 1),
	(103, 'IN00000005', '3No Brick Sale', 100, 2, NULL, NULL, 1, 1),
	(104, 'IN00000006', 'Adhla', 100, 2, NULL, NULL, 1, 1),
	(105, 'EX00000002', 'Labour Payment', NULL, 1, NULL, NULL, 1, 1),
	(106, 'EX00000003', 'Soil Payment', NULL, 1, NULL, NULL, 1, 1),
	(107, 'EX00000004', 'Transport Payment', NULL, 1, NULL, NULL, 1, 1),
	(108, 'EX00000005', 'Govt Fees', NULL, 1, NULL, NULL, 1, 1),
	(109, 'EX00000006', 'Bill', NULL, 1, NULL, NULL, 1, 1),
	(110, 'EX00000007', 'Entertainment', NULL, 1, NULL, NULL, 1, 1),
	(111, 'EX00000008', 'Officie Expense', NULL, 1, NULL, NULL, 1, 1),
	(112, 'EX00000009', 'Miscellaneous', NULL, 1, NULL, NULL, 1, 1),
	(113, 'EX00000010', 'Daily Labour Payment', 105, 1, NULL, NULL, 1, 1),
	(114, 'EX00000011', 'Auto Labour Payment', 105, 1, NULL, NULL, 1, 1),
	(115, 'EX00000012', 'Bangla Labour Payment', 105, 1, NULL, NULL, 1, 1),
	(116, 'EX00000013', 'Load Labour Payment', 105, 1, NULL, NULL, 1, 1),
	(117, 'EX00000014', 'Unload Labour Payment', 105, 1, NULL, NULL, 1, 1),
	(118, 'EX00000015', 'Fire Labour Payment', 105, 1, NULL, NULL, 1, 1),
	(121, 'EX00000017', 'Koyla Purchase', NULL, 1, NULL, NULL, 1, 1),
	(122, 'LI00000002', 'Koyala Supplier (XYZ)', 124, 3, NULL, NULL, 1, 1),
	(123, 'LI00000003', 'Oil Supplier (XYZ)', 124, 3, NULL, NULL, 1, 1),
	(124, 'LI00000004', 'Supplier Liabilities', NULL, 3, NULL, NULL, 1, 1),
	(125, 'LI00000005', 'Bank Loan', NULL, 3, NULL, NULL, 1, 1),
	(126, 'LI00000006', 'Pubali Bank Loan', 125, 3, NULL, NULL, 1, 1),
	(127, 'LI00000007', 'Brack loan', 125, 3, NULL, NULL, 1, 1),
	(128, 'LI00000008', 'DO Customer', NULL, 3, NULL, NULL, 1, 1),
	(129, 'LI00000009', 'General Customer', NULL, 3, NULL, NULL, 1, 1),
	(130, 'LI00000010', 'MOMIT', 128, 3, NULL, NULL, 1, 1),
	(131, 'LI00000011', 'LIPI', 128, 3, NULL, NULL, 1, 1),
	(132, 'LI00000012', 'LIKHI', 129, 3, NULL, NULL, 1, 1),
	(133, 'LI00000013', 'Hasan', 129, 3, NULL, NULL, 1, 1),
	(134, 'LI00000014', 'Labour Payment Due', NULL, 3, NULL, NULL, 1, 1),
	(135, 'LI00000015', 'bangla Labour Payment Due', 134, 3, NULL, NULL, 1, 1),
	(136, 'LI00000016', 'Lend Receivable', NULL, 3, NULL, NULL, 1, 1),
	(137, 'LI00000017', 'Daily Bazar Transection', 136, 3, NULL, NULL, 1, 1),
	(160, 'EX00000018', 'Poribesh Adhidoptor Fee', 108, 1, NULL, NULL, 1, 1),
	(161, 'EX00000019', 'Soil Payments (Belal)', 106, 1, NULL, NULL, 1, 1);
/*!40000 ALTER TABLE `accounting_head` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.accounting_income_expances
CREATE TABLE IF NOT EXISTS `accounting_income_expances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_doc` varchar(500) NOT NULL,
  `amount` float NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `created_by` varchar(55) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `head_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `account_no` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `agreement_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `head_id` (`head_id`),
  CONSTRAINT `accounting_head_fk` FOREIGN KEY (`head_id`) REFERENCES `accounting_head` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.accounting_income_expances: ~0 rows (approximately)
/*!40000 ALTER TABLE `accounting_income_expances` DISABLE KEYS */;
INSERT INTO `accounting_income_expances` (`id`, `date`, `reference_doc`, `amount`, `note`, `created_by`, `created_at`, `head_id`, `stakeholder_id`, `account_no`, `project_id`, `agreement_id`) VALUES
	(1, '2021-06-21 00:00:00', '', 100000, '', '1000001', '2021-06-21 08:08:12', 161, NULL, 1, 12, NULL),
	(2, '2021-06-21 00:00:00', '', 20000, '', '1000001', '2021-06-21 08:08:37', 116, NULL, 1, 12, NULL),
	(3, '2021-05-11 00:00:00', '', 200000, '', '1000001', '2021-06-21 08:10:42', 101, NULL, 1, 12, 0),
	(4, '2021-06-21 00:00:00', '', 50000, '', '1000001', '2021-06-21 08:13:30', 2, 1, 1, 12, NULL),
	(9, '2021-06-21 00:00:00', '', 300000, '', '1000001', '2021-06-21 08:20:57', 1, 2, 1, 12, NULL);
/*!40000 ALTER TABLE `accounting_income_expances` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.appuser
CREATE TABLE IF NOT EXISTS `appuser` (
  `user_id` varchar(12) NOT NULL DEFAULT '',
  `account_no` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL DEFAULT '',
  `user_password` varchar(40) DEFAULT NULL,
  `user_level` varchar(10) NOT NULL DEFAULT 'General' COMMENT 'Admin,Developer,General',
  `login_status` tinyint(4) DEFAULT 0 COMMENT '1=login; 0=not login;',
  `is_active` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Active,0=Blocked',
  `is_owner` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1:owner, 0:user',
  `modified_by` varchar(20) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  KEY `con_user_created_by_fk` (`created_by`),
  KEY `con_user_modified_by_fk` (`modified_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.appuser: ~0 rows (approximately)
/*!40000 ALTER TABLE `appuser` DISABLE KEYS */;
INSERT INTO `appuser` (`user_id`, `account_no`, `user_name`, `user_password`, `user_level`, `login_status`, `is_active`, `is_owner`, `modified_by`, `modified_time`, `created_by`, `expiry_date`, `created_at`) VALUES
	('1000001', 1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'Admin', 0, 1, 1, NULL, NULL, '1000001', NULL, '2018-08-07 13:33:40');
/*!40000 ALTER TABLE `appuser` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.emp_infos
CREATE TABLE IF NOT EXISTS `emp_infos` (
  `emp_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `age` varchar(100) NOT NULL,
  `nid_no` varchar(50) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `contact_no` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `blood_group` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `account_no` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1:active,  0: inactive',
  `created_by` varchar(20) DEFAULT NULL,
  `modified_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.emp_infos: ~0 rows (approximately)
/*!40000 ALTER TABLE `emp_infos` DISABLE KEYS */;
INSERT INTO `emp_infos` (`emp_id`, `full_name`, `address`, `age`, `nid_no`, `photo`, `contact_no`, `email`, `blood_group`, `remarks`, `account_no`, `status`, `created_by`, `modified_by`, `created_at`, `updated_at`) VALUES
	('1000001', 'Mahbub Alam', 'Dhora, Gabtoli', '50', '0', 'images/employee/moumit.jpg', '01712677535', 'mahbub.mithu@gmail.com', 'A+', '', 1, 1, NULL, '1000001', '2018-08-07 15:39:13', '2021-06-21 07:25:46');
/*!40000 ALTER TABLE `emp_infos` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.flat_infos
CREATE TABLE IF NOT EXISTS `flat_infos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flat_name` varchar(150) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `floor_id` int(11) NOT NULL,
  `current_rent` int(11) NOT NULL DEFAULT 0,
  `note` mediumtext DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1:active,  0: inactive',
  `account_no` int(11) DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `modified_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `flat_name_project_id` (`flat_name`,`project_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.flat_infos: ~0 rows (approximately)
/*!40000 ALTER TABLE `flat_infos` DISABLE KEYS */;
/*!40000 ALTER TABLE `flat_infos` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.flat_rent_record
CREATE TABLE IF NOT EXISTS `flat_rent_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flat_id` varchar(150) NOT NULL,
  `old_rent` int(11) NOT NULL DEFAULT 0,
  `current_rent` int(11) NOT NULL DEFAULT 0,
  `created_by` varchar(20) DEFAULT NULL,
  `modified_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `flat_id_old_rent_current_rent` (`flat_id`,`old_rent`,`current_rent`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.flat_rent_record: ~0 rows (approximately)
/*!40000 ALTER TABLE `flat_rent_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `flat_rent_record` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.floors
CREATE TABLE IF NOT EXISTS `floors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `floor_no` varchar(150) NOT NULL,
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.floors: ~9 rows (approximately)
/*!40000 ALTER TABLE `floors` DISABLE KEYS */;
INSERT INTO `floors` (`id`, `floor_no`) VALUES
	(2, 'Ground floor'),
	(3, '1st floor'),
	(4, '2nd floor'),
	(5, '3rd floor'),
	(6, '4th foor'),
	(7, '5th floor'),
	(8, '6th floor'),
	(9, '7th floor'),
	(10, '8th floor');
/*!40000 ALTER TABLE `floors` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.liability
CREATE TABLE IF NOT EXISTS `liability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_doc` varchar(500) NOT NULL,
  `amount` float NOT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `created_by` varchar(55) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `head_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `agreement_id` int(11) DEFAULT NULL,
  `account_no` int(11) DEFAULT NULL,
  `types` tinyint(4) DEFAULT NULL COMMENT '1:paid,  2: received, 3:payable; 4: receivable',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.liability: ~0 rows (approximately)
/*!40000 ALTER TABLE `liability` DISABLE KEYS */;
INSERT INTO `liability` (`id`, `date`, `reference_doc`, `amount`, `note`, `created_by`, `created_at`, `head_id`, `project_id`, `stakeholder_id`, `agreement_id`, `account_no`, `types`) VALUES
	(1, '2021-03-09 00:00:00', '', 500000, '', '1000001', '2021-06-21 08:11:07', 130, 12, 0, NULL, 1, 2),
	(2, '2021-03-09 00:00:00', '', 100000, '', '1000001', '2021-06-21 08:11:27', 130, 12, 0, NULL, 1, 1),
	(3, '2021-06-21 00:00:00', '', 333, '', '1000001', '2021-06-21 08:25:00', 132, 12, 0, NULL, 1, 3),
	(4, '2021-06-21 00:00:00', '', 444, '', '1000001', '2021-06-21 08:25:17', 131, 12, 0, NULL, 1, 4);
/*!40000 ALTER TABLE `liability` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.project_infos
CREATE TABLE IF NOT EXISTS `project_infos` (
  `project_code` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(150) NOT NULL,
  `project_head` int(11) NOT NULL DEFAULT 0,
  `project_address` text DEFAULT NULL,
  `phone` varchar(18) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `logo` longtext DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1:active,  0: inactive',
  `account_no` int(11) NOT NULL DEFAULT 0,
  `created_by` varchar(20) DEFAULT NULL,
  `modified_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`project_code`),
  UNIQUE KEY `con_project_name_uk` (`project_name`),
  KEY `FK1_designition` (`created_by`),
  KEY `FK2_designition` (`modified_by`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.project_infos: ~2 rows (approximately)
/*!40000 ALTER TABLE `project_infos` DISABLE KEYS */;
INSERT INTO `project_infos` (`project_code`, `project_name`, `project_head`, `project_address`, `phone`, `note`, `logo`, `status`, `account_no`, `created_by`, `modified_by`, `created_at`, `updated_at`) VALUES
	(12, 'Molla Bricks', 0, 'Perir Hut, Gabtoli, Bogra', '01712677535', '', '', 1, 1, '1000001', '1000001', '2021-02-16 16:35:11', '2021-06-21 07:22:55');
/*!40000 ALTER TABLE `project_infos` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.renter
CREATE TABLE IF NOT EXISTS `renter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `address` mediumtext NOT NULL,
  `age` varchar(100) NOT NULL,
  `nid_no` varchar(50) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `contact_no` varchar(50) NOT NULL,
  `blood_group` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `remarks` mediumtext DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1:active,  0: inactive',
  `account_no` int(11) DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `modified_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.renter: ~0 rows (approximately)
/*!40000 ALTER TABLE `renter` DISABLE KEYS */;
/*!40000 ALTER TABLE `renter` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.renter_flat_agreement
CREATE TABLE IF NOT EXISTS `renter_flat_agreement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `renter_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `flat_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `current_rent` int(11) NOT NULL DEFAULT 0,
  `advance` int(11) NOT NULL DEFAULT 0,
  `account_no` int(11) NOT NULL,
  `agreement` varchar(250) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1:active,  0: inactive',
  `created_by` varchar(20) DEFAULT NULL,
  `modified_by` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.renter_flat_agreement: ~0 rows (approximately)
/*!40000 ALTER TABLE `renter_flat_agreement` DISABLE KEYS */;
/*!40000 ALTER TABLE `renter_flat_agreement` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.stakeholders
CREATE TABLE IF NOT EXISTS `stakeholders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0:deactive, 1:active',
  `account_no` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.stakeholders: ~3 rows (approximately)
/*!40000 ALTER TABLE `stakeholders` DISABLE KEYS */;
INSERT INTO `stakeholders` (`id`, `name`, `mobile`, `email`, `status`, `account_no`) VALUES
	(1, 'Saju', '01980340480', 'saju@gmail.com', 1, 1),
	(2, 'Mahabub Alam Mithu', '01712677535', 'mithu.mahbub@gmail.com', 1, 1);
/*!40000 ALTER TABLE `stakeholders` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.user_group
CREATE TABLE IF NOT EXISTS `user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(100) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL COMMENT '0:active, 1:inactive',
  `editable` tinyint(1) DEFAULT 0 COMMENT '1:editable,0:not editable',
  `account_no` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.user_group: ~3 rows (approximately)
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
INSERT INTO `user_group` (`id`, `group_name`, `status`, `editable`, `account_no`) VALUES
	(14, 'Admin', 0, 0, 0),
	(27, 'Accountant', 0, 0, 0),
	(28, 'Stakeholder', 0, 0, 0);
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.user_group_member
CREATE TABLE IF NOT EXISTS `user_group_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT 0,
  `emp_id` varchar(20) NOT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '0: no access ; 1:access',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`group_id`,`emp_id`),
  KEY `FK_emp_infos` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.user_group_member: ~3 rows (approximately)
/*!40000 ALTER TABLE `user_group_member` DISABLE KEYS */;
INSERT INTO `user_group_member` (`id`, `group_id`, `emp_id`, `status`) VALUES
	(10, 14, '1000001', 1),
	(11, 27, '1000001', 0),
	(13, 28, '1000001', 0);
/*!40000 ALTER TABLE `user_group_member` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.user_group_permission
CREATE TABLE IF NOT EXISTS `user_group_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) DEFAULT 0,
  `action_id` int(11) DEFAULT 0,
  `status` tinyint(1) NOT NULL COMMENT '0: Not Permit, 1: Permit',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`group_id`,`action_id`),
  KEY `FK__activity_actions` (`action_id`)
) ENGINE=InnoDB AUTO_INCREMENT=768 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.user_group_permission: ~141 rows (approximately)
/*!40000 ALTER TABLE `user_group_permission` DISABLE KEYS */;
INSERT INTO `user_group_permission` (`id`, `group_id`, `action_id`, `status`) VALUES
	(241, 14, 10, 1),
	(242, 14, 15, 1),
	(243, 14, 11, 1),
	(244, 14, 12, 1),
	(245, 14, 13, 1),
	(246, 14, 14, 1),
	(247, 14, 16, 1),
	(248, 14, 43, 1),
	(249, 14, 44, 1),
	(275, 14, 53, 1),
	(277, 14, 54, 1),
	(279, 14, 55, 1),
	(281, 14, 56, 1),
	(295, 14, 63, 1),
	(327, 14, 79, 1),
	(329, 14, 80, 1),
	(331, 14, 81, 1),
	(333, 14, 82, 1),
	(335, 14, 83, 1),
	(337, 14, 84, 1),
	(339, 14, 85, 1),
	(341, 14, 86, 1),
	(369, 14, 100, 1),
	(373, 14, 102, 1),
	(375, 14, 103, 1),
	(377, 14, 104, 1),
	(379, 14, 105, 1),
	(381, 14, 106, 1),
	(383, 14, 107, 1),
	(385, 14, 108, 1),
	(387, 14, 109, 1),
	(391, 14, 111, 1),
	(545, 14, 114, 1),
	(549, 14, 115, 1),
	(553, 14, 116, 1),
	(662, 27, 10, 0),
	(663, 27, 11, 0),
	(664, 27, 12, 0),
	(665, 27, 13, 0),
	(666, 27, 14, 0),
	(667, 27, 15, 0),
	(668, 27, 16, 0),
	(669, 27, 43, 0),
	(670, 27, 44, 0),
	(671, 27, 53, 0),
	(672, 27, 54, 0),
	(673, 27, 55, 0),
	(674, 27, 56, 0),
	(675, 27, 63, 0),
	(676, 27, 79, 1),
	(677, 27, 80, 1),
	(678, 27, 81, 1),
	(679, 27, 82, 1),
	(680, 27, 83, 1),
	(681, 27, 84, 1),
	(682, 27, 85, 1),
	(683, 27, 86, 1),
	(684, 27, 100, 1),
	(685, 27, 102, 1),
	(686, 27, 103, 1),
	(687, 27, 104, 1),
	(688, 27, 105, 1),
	(689, 27, 106, 1),
	(690, 27, 107, 1),
	(691, 27, 108, 1),
	(692, 27, 109, 1),
	(693, 27, 111, 1),
	(694, 27, 114, 1),
	(695, 27, 115, 1),
	(696, 27, 116, 1),
	(697, 28, 10, 0),
	(698, 28, 11, 0),
	(699, 28, 12, 0),
	(700, 28, 13, 0),
	(701, 28, 14, 0),
	(702, 28, 15, 0),
	(703, 28, 16, 0),
	(704, 28, 43, 0),
	(705, 28, 44, 0),
	(706, 28, 53, 0),
	(707, 28, 54, 0),
	(708, 28, 55, 0),
	(709, 28, 56, 0),
	(710, 28, 63, 0),
	(711, 28, 79, 0),
	(712, 28, 80, 0),
	(713, 28, 81, 0),
	(714, 28, 82, 0),
	(715, 28, 83, 0),
	(716, 28, 84, 0),
	(717, 28, 85, 0),
	(718, 28, 86, 1),
	(719, 28, 100, 1),
	(720, 28, 102, 0),
	(721, 28, 103, 0),
	(722, 28, 104, 0),
	(723, 28, 105, 1),
	(724, 28, 106, 0),
	(725, 28, 107, 0),
	(726, 28, 108, 0),
	(727, 28, 109, 1),
	(728, 28, 111, 1),
	(729, 28, 114, 1),
	(730, 28, 115, 1),
	(731, 28, 116, 1),
	(732, 14, 117, 1),
	(733, 27, 117, 0),
	(734, 28, 117, 0),
	(735, 14, 118, 1),
	(736, 27, 118, 0),
	(737, 28, 118, 0),
	(738, 14, 119, 1),
	(739, 27, 119, 0),
	(740, 28, 119, 0),
	(741, 14, 120, 1),
	(742, 27, 120, 0),
	(743, 28, 120, 0),
	(744, 14, 121, 1),
	(745, 27, 121, 0),
	(746, 28, 121, 0),
	(747, 14, 122, 1),
	(748, 27, 122, 0),
	(749, 28, 122, 0),
	(750, 14, 123, 1),
	(751, 27, 123, 0),
	(752, 28, 123, 0),
	(753, 14, 124, 1),
	(754, 27, 124, 0),
	(755, 28, 124, 0),
	(756, 14, 125, 1),
	(757, 27, 125, 0),
	(758, 28, 125, 0),
	(759, 14, 126, 1),
	(760, 27, 126, 0),
	(761, 28, 126, 0),
	(762, 14, 127, 1),
	(763, 27, 127, 0),
	(764, 28, 127, 0),
	(765, 14, 128, 1),
	(766, 27, 128, 0),
	(767, 28, 128, 0);
/*!40000 ALTER TABLE `user_group_permission` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.web_actions
CREATE TABLE IF NOT EXISTS `web_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_name` varchar(50) NOT NULL,
  `module_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:active, 1:inactive',
  PRIMARY KEY (`id`),
  UNIQUE KEY `activity_name_module_id` (`activity_name`,`module_id`),
  KEY `module_id` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.web_actions: ~47 rows (approximately)
/*!40000 ALTER TABLE `web_actions` DISABLE KEYS */;
INSERT INTO `web_actions` (`id`, `activity_name`, `module_id`, `status`) VALUES
	(10, 'User Add', 1, 0),
	(11, 'User permission', 1, 0),
	(12, 'User update', 1, 0),
	(13, 'User delete', 1, 0),
	(14, 'Control panel', 1, 0),
	(15, 'User List', 1, 0),
	(16, 'Permission grid', 1, 0),
	(43, 'Action access', 1, 0),
	(44, 'Group permission', 1, 0),
	(53, 'Project Entry', 4, 0),
	(54, 'Project Delete', 4, 0),
	(55, 'Project Update', 4, 0),
	(56, 'Project List', 4, 0),
	(63, 'Project Permission', 4, 0),
	(79, 'Head Entry', 3, 0),
	(80, 'Head Update', 3, 0),
	(81, 'Head Delete', 3, 0),
	(82, 'Head List', 3, 0),
	(83, 'Entry', 3, 0),
	(84, 'Update', 3, 0),
	(85, 'Delete', 3, 0),
	(86, 'Expense List', 3, 0),
	(100, 'Expense Report', 2, 0),
	(102, 'Expenses & Income Entry', 3, 0),
	(103, 'Expenses & Income Update', 3, 0),
	(104, 'Expenses & Income Delete', 3, 0),
	(105, 'Expenses & Income List', 3, 0),
	(106, 'Expenses & Income Head Entry', 3, 0),
	(107, 'Expenses & Income Head Update', 3, 0),
	(108, 'Expenses & Income Head Delete', 3, 0),
	(109, 'Expenses & Income Head List', 3, 0),
	(111, 'Invoice view', 2, 0),
	(114, 'Invoice Report', 2, 0),
	(115, 'Expense VS Income Report (Hotel)', 2, 0),
	(116, 'Expense VS Income Report (Company)', 2, 0),
	(117, 'Renter Entry', 4, 0),
	(118, 'Renter List', 4, 0),
	(119, 'Renter Update', 4, 0),
	(120, 'Renter Delete', 4, 0),
	(121, 'Flat Entry', 4, 0),
	(122, 'Flat List', 4, 0),
	(123, 'Flat update', 4, 0),
	(124, 'Flat Delete', 4, 0),
	(125, 'agreement list', 4, 0),
	(126, 'agreement entry', 4, 0),
	(127, 'agreement update', 4, 0),
	(128, 'agreement delete', 4, 0);
/*!40000 ALTER TABLE `web_actions` ENABLE KEYS */;

-- Dumping structure for table mollabricks-accounting_new.web_module
CREATE TABLE IF NOT EXISTS `web_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:active, 1:inactive',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table mollabricks-accounting_new.web_module: ~4 rows (approximately)
/*!40000 ALTER TABLE `web_module` DISABLE KEYS */;
INSERT INTO `web_module` (`id`, `module_name`, `status`) VALUES
	(1, 'Users', 0),
	(2, 'Report', 0),
	(3, 'Accounting', 0),
	(4, 'Project', 0);
/*!40000 ALTER TABLE `web_module` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
