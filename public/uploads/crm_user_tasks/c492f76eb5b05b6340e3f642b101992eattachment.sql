-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `hrms_approver_chain_steps`;
CREATE TABLE `hrms_approver_chain_steps` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `approver_chain_id` int NOT NULL,
  `step_no` int NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approver_id` int DEFAULT NULL,
  `status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P' COMMENT 'P=Pending, A=Approved, R=Rejected, S=Skipped',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `acted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `l1_workload` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_coverage` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_eligibility` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_duration_ok` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_notice_compliance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_decision` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l1_remark` text COLLATE utf8mb4_unicode_ci,
  `l2_balance` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_unpaid` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_encash` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_cost` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_policy` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_decision` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l2_remark` text COLLATE utf8mb4_unicode_ci,
  `l3_docs` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_policy` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_system` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_payroll` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_legal` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_decision` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l3_remark` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chain_step_unique` (`approver_chain_id`,`step_no`),
  KEY `chain_idx` (`approver_chain_id`),
  KEY `approver_idx` (`approver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hrms_approver_chain_steps` (`id`, `approver_chain_id`, `step_no`, `role`, `approver_id`, `status`, `comment`, `acted_at`, `created_at`, `updated_at`, `l1_workload`, `l1_coverage`, `l1_eligibility`, `l1_duration_ok`, `l1_notice_compliance`, `l1_decision`, `l1_remark`, `l2_balance`, `l2_unpaid`, `l2_encash`, `l2_cost`, `l2_policy`, `l2_decision`, `l2_remark`, `l3_docs`, `l3_policy`, `l3_system`, `l3_payroll`, `l3_legal`, `l3_decision`, `l3_remark`) VALUES
(32,	12,	1,	'Reporting Manager',	2,	'P',	NULL,	NULL,	'2025-10-08 07:43:35',	'2025-10-08 07:43:35',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(33,	12,	2,	'HR',	98,	'P',	NULL,	NULL,	'2025-10-08 07:43:35',	'2025-10-08 07:43:35',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(34,	12,	3,	'Finance',	27,	'P',	NULL,	NULL,	'2025-10-08 07:43:35',	'2025-10-08 07:43:35',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(35,	13,	1,	'Reporting Manager',	5,	'P',	NULL,	NULL,	'2025-10-08 10:45:03',	'2025-10-08 10:45:03',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(36,	13,	2,	'HR',	98,	'P',	NULL,	NULL,	'2025-10-08 10:45:03',	'2025-10-08 10:45:03',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(37,	13,	3,	'Finance',	27,	'P',	NULL,	NULL,	'2025-10-08 10:45:03',	'2025-10-08 10:45:03',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `hrms_approver_chains`;
CREATE TABLE `hrms_approver_chains` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `leave_request_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `overall_status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P' COMMENT 'P=Pending, A=Approved, R=Rejected',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_request_unique` (`leave_request_id`),
  KEY `staff_idx` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hrms_approver_chains` (`id`, `leave_request_id`, `staff_id`, `overall_status`, `created_at`, `updated_at`) VALUES
(12,	37,	109,	'P',	'2025-10-08 07:43:35',	'2025-10-08 07:43:35'),
(13,	38,	109,	'P',	'2025-10-08 10:45:03',	'2025-10-08 10:45:03');

DROP TABLE IF EXISTS `sm_leave_defines`;
CREATE TABLE `sm_leave_defines` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint DEFAULT NULL,
  `type_id` tinyint DEFAULT NULL,
  `days` int DEFAULT NULL,
  `active_status` tinyint NOT NULL DEFAULT '1',
  `created_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sm_leave_requests`;
CREATE TABLE `sm_leave_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `leave_define_id` int DEFAULT NULL,
  `staff_id` int unsigned DEFAULT NULL,
  `reporting_manager_id` int unsigned NOT NULL,
  `handover_to` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int unsigned DEFAULT NULL,
  `apply_date` date DEFAULT NULL,
  `leave_year` year DEFAULT NULL,
  `type_id` tinyint DEFAULT NULL,
  `leave_from` date DEFAULT NULL,
  `leave_to` date DEFAULT NULL,
  `days` decimal(5,2) DEFAULT NULL,
  `is_half_day` tinyint(1) NOT NULL DEFAULT '0',
  `half_session` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `emergency_contacts` json DEFAULT NULL,
  `file` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approve_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'P for Pending, A for Approve, R for reject',
  `approver_chain` text COLLATE utf8mb4_unicode_ci,
  `current_index` tinyint NOT NULL DEFAULT '0',
  `approvals_json` text COLLATE utf8mb4_unicode_ci,
  `approved_by` int unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` int unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `active_status` tinyint NOT NULL DEFAULT '1',
  `company_id` int NOT NULL,
  `created_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `updated_by` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_slr_staff_dates` (`staff_id`,`leave_from`,`leave_to`),
  KEY `idx_slr_status` (`approve_status`),
  KEY `idx_slr_type_year` (`type_id`,`leave_year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sm_leave_types`;
CREATE TABLE `sm_leave_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text,
  `max_days_per_year` decimal(5,2) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT '1',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- 2025-10-09 11:40:46
