-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 31, 2025 at 01:41 PM
-- Server version: 8.0.36
-- PHP Version: 8.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `neoehs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `osh_audit_plan`
--

CREATE TABLE `osh_audit_plan` (
  `id` int NOT NULL,
  `audit_plan_no` varchar(512) DEFAULT NULL,
  `auditor_emp_id` int NOT NULL,
  `auditor_emp_role` int DEFAULT NULL,
  `aud_type_id` int NOT NULL,
  `standards` varchar(255) DEFAULT NULL,
  `audit_objective` varchar(255) DEFAULT NULL,
  `audit_scope` varchar(255) DEFAULT NULL COMMENT '0,1,2',
  `audit_criteria` varchar(255) DEFAULT NULL,
  `form_status` int DEFAULT NULL COMMENT '1-waiting for approval,2-rejected,3-approved,4-reschedule',
  `rejected_comments` text,
  `audit_created_by` int DEFAULT NULL,
  `audit_created_role` int DEFAULT NULL,
  `audit_plan_created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_date` date DEFAULT NULL,
  `audit_plan_updated_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `audit_plan_status` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `osh_audit_plan`
--

INSERT INTO `osh_audit_plan` (`id`, `audit_plan_no`, `auditor_emp_id`, `auditor_emp_role`, `aud_type_id`, `standards`, `audit_objective`, `audit_scope`, `audit_criteria`, `form_status`, `rejected_comments`, `audit_created_by`, `audit_created_role`, `audit_plan_created_on`, `created_date`, `audit_plan_updated_on`, `audit_plan_status`) VALUES
(1, 'AUD-00001', 411, 2, 5, 'ISO 9001 ISO 14001 ISO 45001', 'To evaluate existing integrated management system.', '', '', 3, '', 411, 2, '2024-09-07 09:07:57', NULL, '2024-11-21 17:13:20', 1),
(2, 'AUD-00002', 411, 2, 19, 'ISO 9001, ISO 14001, ISO 45001', 'To evaluate IMS (Integrated Management System) of DB', 'Station operations process', '1. Applicable O&M agreement requirements\' and  document\'s (Policy, manual, Plan, Procedures etc.)', 1, NULL, 411, 2, '2024-09-09 14:17:07', NULL, '2024-11-21 17:12:36', 0),
(3, 'AUD-00002', 411, 2, 19, 'ISO 9001, ISO 14001, ISO 45001', 'To evaluate IMS (Integrated Management System) of DB', 'Station operations process', '1) Applicable O&M agreement requirements\' 2) Document\'s (Policy, manual, Plan, Procedures etc.)', 3, '', 411, 2, '2024-09-09 14:17:08', NULL, '2024-09-09 14:52:57', 1),
(4, 'AUD-00003', 411, 2, 20, 'ISO 9001, ISO 14001, ISO 45001', 'To evaluate IMS (Integrated Management System) of DB', 'Station operations process', '1) Applicable O&M agreement requirements\' 2) Document\'s (Policy, manual, Plan, Procedures etc.)', 3, '', 411, 2, '2024-09-12 12:34:06', NULL, '2024-09-12 14:59:37', 1),
(5, 'AUD-00004', 1, 1, 20, 'ISO 9001, ISO 14001, ISO 45001', 'To evaluate IMS (Integrated Management System) of DB', 'Station operations process', '1) Applicable O&M agreement requirements\' 2) Document\'s (Policy, manual, Plan, Procedures etc.)', 3, '', 1, 1, '2024-09-13 10:50:49', NULL, '2024-09-13 10:53:59', 1),
(6, 'AUD-00005', 411, 2, 19, '', 'To review the documentation ', '', '', 3, '', 411, 2, '2024-09-17 14:37:51', NULL, '2024-09-17 14:38:27', 1),
(7, 'AUD-00006', 205, 2, 21, '', '', '', '', 3, '', 205, 2, '2024-09-23 11:01:53', NULL, '2024-09-23 11:05:13', 1),
(8, 'AUD-00007', 1, 1, 19, '', 'To evaluate the Management system', '', '', 3, '', 1, 1, '2024-10-23 16:19:16', NULL, '2024-10-23 16:20:10', 1),
(9, 'AUD-00008', 1, 1, 20, 'ISO 9001, ISO 14001 and ISO 45001', 'To evaluate IMS (Integrated Management  System) of DB', '', '1) Applicable O&M agreement requirements\'  2) Document\'s (Policy, manual, Plan,  Procedures etc.)', 3, '', 1, 1, '2024-10-24 13:48:26', NULL, '2024-11-28 12:54:15', 1),
(10, 'AUD-00009', 411, 2, 20, 'ISO 9001, ISO 14001 and ISO 45001', 'To evaluate IMS (Integrated Management  System) of DB', '', '1) Applicable O&M agreement requirements\'  2) Document\'s (Policy, manual, Plan,  Procedures etc.)', 3, '', 411, 2, '2024-10-24 13:53:04', NULL, '2024-10-24 14:14:03', 1),
(11, 'AUD-00010', 1, 1, 21, '', 'adfs', '', '', 1, NULL, 1, 1, '2024-10-25 15:26:17', NULL, '2025-01-31 11:15:06', 0),
(12, 'AUD-00011', 253, 2, 21, '', '1) To confirm that the management system&#40;s&#41; conforms with requirements of the standard(s); 2) To confirm that the organization has effectively implemented its planned arrangements; 3) To confirm that all legal and other requirements are met by the', 'Physical compliance verification at site, Interview with the auditee, Records checking', 'IMS Standards, Compliance Requirements; Safety Manual, Maintenance Manual, relevant SOPs, Station Working Order', 3, '', 253, 2, '2024-11-06 10:51:32', NULL, '2024-11-06 10:54:48', 1),
(15, 'AUD-00012', 318, 2, 22, 'ISO 9001,1401,4501', 'to verify the documents and sop', 'Station operation process', 'o&m agreement reqirement', 3, '', 318, 2, '2024-11-08 14:33:44', NULL, '2024-11-08 14:34:46', 1),
(16, 'AUD-00013', 601, 2, 21, '', '1) To confirm that the management system&#40;s&#41; conforms with requirements of the standard(s)2) To confirm that the organization has effectively implemented its planned arrangements.', 'Physical compliance verification at site, Interview with the auditee.', 'IMS Standards, Compliance Requirements; Safety Manual, Maintenance Manual, relevant SOPs, Station Working Order', 3, '', 601, 2, '2024-11-14 08:38:42', NULL, '2024-11-14 09:49:38', 1),
(17, 'AUD-00014', 411, 2, 20, 'ISO 9001, ISO 14001, ISO 45001 ', 'To evaluate IMS (Integrated Management System) of DB', 'MEP Process', '1) Applicable O&M agreement requirement\'s 2) Document\'s (Policy, manual, Plan, procedures etc.)', 3, '', 411, 2, '2024-11-18 12:15:26', NULL, '2024-11-18 16:49:08', 1),
(18, 'AUD-00015', 411, 2, 20, 'ISO 9001, ISO 14001, ISO 45001', 'To evaluate IMS (Integrated Management System) of DB', 'Station Operation Process', '1) Applicable O&M agreement requirement\'s 2) Document\'s (Policy, manual, Plan, procedures etc.)', 3, '', 411, 2, '2024-11-18 14:11:04', NULL, '2024-11-19 13:03:54', 1),
(19, 'AUD-00016', 411, 2, 20, 'ISO 9001, ISO 14001, ISO 45001', 'To evaluate IMS (Integrated Management System) of DB', 'Station Process', '1) Applicable O&M agreement requirement\'s 2) Document\'s (Policy, manual, Plan, procedures etc.)', 3, '', 411, 2, '2024-11-18 14:19:29', NULL, '2024-11-19 13:05:45', 1),
(21, 'AUD-00017', 411, 2, 20, 'ISO 9001, ISO 14001, ISO 45001', 'To evaluate IMS (Integrated Management System) of DB', 'PST Processes', '1) Applicable O&M agreement requirement\'s 2) Document\'s (Policy, manual, Plan, procedures etc.)', 3, '', 411, 2, '2024-11-22 11:41:50', NULL, '2024-11-22 11:44:56', 1),
(22, 'AUD-00018', 318, 2, 22, 'ISO 9001,1401,4501', 'to verify the documents and sop', 'Station operation process', 'o&m agreement reqirement', 3, '', 318, 2, '2024-12-12 17:00:58', NULL, '2024-12-18 11:00:51', 1),
(23, 'AUD-00019', 199, 2, 23, '', '', 'To improve the system and staff awareness', '', 3, '', 199, 2, '2024-12-18 17:14:52', NULL, '2024-12-18 17:16:47', 1),
(24, 'AUD-00020', 199, 2, 23, '', '', 'To improve the system and staff awareness', '', 1, NULL, 199, 2, '2024-12-18 17:16:48', NULL, '2025-01-30 12:05:00', 0),
(25, 'AUD-00021', 1, 1, 20, 'ISO 9001:2015, ISO 14001:2015 and ISO 45001:2028', 'To verify the implemented management system', 'Station and other associated processes ', 'O&M Agreement and ISO Manuals', 3, '', 1, 1, '2024-12-20 10:55:57', NULL, '2024-12-23 15:01:39', 1),
(26, 'AUD-00022', 601, 2, 21, NULL, '1) To confirm that the management system&#40;s&#41; conforms with requirements of the standard(s); 2) To confirm that the organization has effectively implemented its planned arrangements; 3) To confirm that all legal and other requirements are met by the', 'Physical compliance verification at site, Interview with the auditee, Records check-in. ', 'IMS Standards, Compliance Requirements; Safety Manual, Maintenance Manual, relevant SOPs, Station Working Orde', 3, '', 601, 2, '2024-12-23 11:26:53', NULL, '2024-12-24 10:06:32', 1),
(27, 'AUD-00023', 260, 2, 19, 'ISO 9001, ISO 14001, ISO 45001', 'To verify  implemented management system.', 'Station and other associated process', 'O&M Agreement and IMS Manual', 3, '', 260, 2, '2024-12-27 12:52:40', NULL, '2024-12-27 12:54:12', 1),
(28, 'AUD-00024', 1, 1, 20, 'ISO 9001:2015, ISO 14001:2015 and ISO 45001:2018', 'To Verifiy Implemented Management System', 'Station Operation & Associated Process ', 'O&M Agreement and IMS Manual', 1, NULL, 1, 1, '2024-12-27 14:24:31', NULL, '2024-12-27 14:24:42', 0),
(29, 'AUD-00025', 411, 2, 20, 'ISO 9001:2015, ISO 14001:2015 and ISO 45001:2018', 'To verify implemented management system', 'Station operation and associated procesess', 'O&M Agreement, IMS Manual', 3, '', 411, 2, '2024-12-27 14:36:14', NULL, '2024-12-27 14:38:02', 1),
(30, 'AUD-00026', 198, 2, 23, 'ISO9001:14001 and 45001', 'Inspection of depot assets with CEO', 'signaling inspection', 'assets and mantenance inspection ', 3, '', 198, 2, '2025-01-09 21:54:01', NULL, '2025-01-09 21:54:41', 1),
(31, 'AUD-00027', 318, 2, 22, 'ISO 9001,1401,4501', 'to verify the documents and sop', 'Station operation process', 'o&m agreement reqirement', 3, '', 318, 2, '2025-01-13 16:29:00', NULL, '2025-01-15 12:23:25', 1),
(32, 'AUD-00028', 253, 2, 20, '', '1) To confirm that the management system&#40;s&#41; conforms with requirements of the standard(s); 2) To confirm that the organization has effectively implemented its planned arrangements; 3) To confirm that all legal and other requirements are met by the', 'Physical compliance verification at site, Interview with the auditee, Records checking', 'IMS Standards, Compliance Requirements; Safety Manual, Maintenance Manual, relevant SOPs, Station Working Order', 3, '', 253, 2, '2025-01-14 15:50:44', NULL, '2025-01-14 15:55:46', 1),
(33, 'AUD-00029', 586, 2, 22, 'ISO 9k,14k,45k', 'To Verify the Documents and System', 'Station Operation', 'Station', 3, '', 586, 2, '2025-01-15 09:32:20', NULL, '2025-01-15 09:33:21', 1),
(34, 'AUD-00030', 260, 2, 19, 'ISO 9001, ISO 14001, ISO 45001', 'To verify  implemented management system.', 'Station and other associated process', 'O&M Agreement and IMS Manual', 3, '', 260, 2, '2025-01-16 15:52:13', NULL, '2025-01-16 16:01:01', 1),
(35, 'AUD-00031', 586, 2, 22, 'ISO 9k,14k,45k', 'To Verify the Documents and System', 'Station Operation', 'Station', 3, '', 586, 2, '2025-01-16 16:47:34', NULL, '2025-01-16 16:56:17', 1),
(36, 'AUD-00031', 411, 2, 20, 'ISO 9001, ISO 14001 and ISO 45001', 'To verify Implemented Management System', 'Station Operation Process', 'O&M agreement and IMS Manual', 3, '', 411, 2, '2025-01-16 16:48:58', NULL, '2025-01-17 09:22:04', 1),
(37, 'AUD-00032', 253, 2, 20, '', '1) To confirm that the management system&#40;s&#41; conforms with requirements of the standard(s); 2) To confirm that the organization has effectively implemented its planned arrangements; 3) To confirm that all legal and other requirements are met by the', 'Physical compliance verification at site, Interview with the auditee, Records checking', 'IMS Standards, Compliance Requirements; Safety Manual, Maintenance Manual, relevant SOPs, Station Working Order', 3, '', 253, 2, '2025-01-18 12:35:50', NULL, '2025-01-18 12:47:21', 1),
(38, 'AUD-00033', 200, 2, 23, '', 'General inspection of GZB RSS', 'RSS', 'O&M agreement', 3, '', 200, 2, '2025-01-20 12:51:42', NULL, '2025-01-20 12:52:47', 1),
(39, 'AUD-00034', 586, 2, 22, 'ISO 9k,14k,45k', 'To Verify the Documents and System', 'Station Operation', 'Station', 3, '', 586, 2, '2025-01-21 09:33:57', NULL, '2025-01-21 09:37:26', 1),
(40, 'AUD-00035', 411, 2, 20, 'ISO 9001:2015, ISO 14001:2015 and ISO 45001:2018', 'To verify the Implemented management System ', 'Train Operation and Crew Control Process', 'O&M agreement and IMS Manual', 3, '', 411, 2, '2025-01-21 15:28:31', NULL, '2025-01-22 15:23:01', 1),
(41, 'AUD-00036', 586, 2, 22, 'ISO 9k,14k,45k', 'To Verify the Documents and System', 'Station Operation', 'Station', 3, '', 586, 2, '2025-01-23 16:41:58', NULL, '2025-01-23 17:25:44', 1),
(42, 'AUD-00037', 187, 2, 23, '', 'Attend the failure related with 96core fiber at A08-A09', 'LTE SYSTEM', 'CM', 3, '', 187, 2, '2025-01-23 17:18:34', NULL, '2025-01-23 17:19:11', 1),
(43, 'AUD-00038', 187, 2, 23, '', 'HHT DATA VERIFICATION', 'LTE SYSTEM', 'INSPECTION/AUDIT', 3, '', 187, 2, '2025-01-24 10:08:18', NULL, '2025-01-24 10:13:46', 1),
(44, 'AUD-00039', 544, 2, 23, 'IS9000, IS14000, IS45000', 'Monthly Foot Plate Inspection (Cab Inspection)', 'AS2 to A06 and A01 to A10 UP & DN Line', 'Cab Inspection', 1, NULL, 544, 2, '2025-01-27 10:18:11', NULL, '2025-01-27 10:36:19', 0),
(45, 'AUD-00040', 411, 2, 20, 'ISO 9001:2015, ISO 14001:2015 and ISO 45001:2018', 'To verify implemented management system and documents', 'Station Operation & Associated Process ', 'O&M Agreement and ISO Manual\'s', 3, '', 411, 2, '2025-01-27 11:14:12', NULL, '2025-01-27 11:16:57', 1),
(46, 'AUD-00041', 584, 2, 23, 'ISO9001, ISO14001,ISO45001', 'Testing of Train Onboard Equipment', 'Signaling', 'Onboard Manual', 3, '', 584, 2, '2025-01-30 10:41:55', NULL, '2025-01-30 10:42:41', 1),
(47, 'AUD-00042', 584, 2, 23, '', '', '', '', 3, '', 584, 2, '2025-01-30 11:01:28', NULL, '2025-01-31 11:14:18', 1),
(48, 'AUD-00043', 584, 2, 23, 'ISO9001, ISO14001,ISO45001', '', 'Signaling', 'Onboard Manual', 3, '', 584, 2, '2025-01-30 11:04:57', NULL, '2025-01-31 11:14:01', 1),
(49, 'AUD-00044', 1, 1, 20, 'ISO 9001:2015, ISO 14001:2015 ISO 45001:2018', 'To verify Implemented Management System  ', 'Station Process and related Process', 'O&M Agreement and IMS Manual', 3, '', 1, 1, '2025-01-30 12:18:20', NULL, '2025-01-30 12:18:46', 1),
(50, 'AUD-00045', 584, 2, 23, 'ISO9001, ISO14001,ISO45001', 'POINT FAILURE', 'Signaling', 'Onboard Manual', 1, NULL, 584, 2, '2025-01-31 12:52:09', NULL, '2025-01-31 12:52:09', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `osh_audit_plan`
--
ALTER TABLE `osh_audit_plan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `osh_audit_plan`
--
ALTER TABLE `osh_audit_plan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
