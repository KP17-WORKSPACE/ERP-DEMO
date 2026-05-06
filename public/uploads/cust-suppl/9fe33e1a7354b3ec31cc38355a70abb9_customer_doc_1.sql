-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `sys_company_addresses`;
CREATE TABLE `sys_company_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `address_type` enum('registered','corporate','branch','warehouse','factory','billing','shipping','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'registered',
  `label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `building` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_line_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_company_addresses_company_id` (`company_id`),
  CONSTRAINT `fk_company_addresses_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sys_company_banking`;
CREATE TABLE `sys_company_banking` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `bank_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iban_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `swift_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `finance_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_letter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_banking_company` (`company_id`),
  CONSTRAINT `fk_banking_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sys_company_banking` (`id`, `company_id`, `bank_name`, `branch_name`, `account_number`, `iban_number`, `swift_code`, `finance_code`, `currency`, `bank_letter`, `created_at`, `updated_at`, `deleted_at`) VALUES
(56,	280,	'kotak',	'kotakkk',	'98938398383',	'9898998',	'98989',	'989',	'1',	'uploads/company/banking_letters/kKD74JkIGxyAsiC1qqhLeJypjRmhTHu02qwVbwQS.jpeg',	'2025-12-03 17:52:16',	'2025-12-03 17:52:16',	NULL),
(57,	280,	'Axis',	'axis',	'3434343',	'9898998',	'98989',	'989',	'1',	'uploads/company/banking_letters/j6hNKoTrZcdzivgK1pnAONFQ6O3B1mw4V5yMJRsD.jpeg',	'2025-12-03 17:57:32',	'2025-12-03 17:57:32',	NULL);

DROP TABLE IF EXISTS `sys_company_compliances`;
CREATE TABLE `sys_company_compliances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `trade_license_no` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_issue_date` date DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  `issuing_authority` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_applicable` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_registration_number` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_percentage` decimal(5,2) DEFAULT NULL,
  `vat_certificate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_issuing_authority` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corporate_tax_number` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corporate_tax_date` date DEFAULT NULL,
  `corporate_tax_vat` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corporate_tax_certificate` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `corporate_issuing_authority` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_company_compliances_company` (`company_id`),
  CONSTRAINT `fk_company_compliances_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sys_company_compliances` (`id`, `company_id`, `trade_license_no`, `license_issue_date`, `license_expiry_date`, `issuing_authority`, `tax_applicable`, `vat_registration_number`, `vat_percentage`, `vat_certificate`, `vat_issuing_authority`, `corporate_tax_number`, `corporate_tax_date`, `corporate_tax_vat`, `corporate_tax_certificate`, `corporate_issuing_authority`, `vat_date`, `created_at`, `updated_at`) VALUES
(108,	280,	'112233',	'2025-12-12',	'2025-12-19',	'12121',	NULL,	'1',	1.00,	'uploads/company/vat_6930802263afc.jpg',	NULL,	'',	NULL,	'',	NULL,	NULL,	'2025-12-20',	'2025-12-03 17:51:53',	'2025-12-03 18:53:30');

DROP TABLE IF EXISTS `sys_company_documents`;
CREATE TABLE `sys_company_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `establishment_file` varchar(255) DEFAULT NULL,
  `establishment_expiry` date DEFAULT NULL,
  `establishment_number` varchar(100) DEFAULT NULL,
  `immigration_file` varchar(255) DEFAULT NULL,
  `immigration_expiry` date DEFAULT NULL,
  `immigration_number` varchar(100) DEFAULT NULL,
  `labour_file` varchar(255) DEFAULT NULL,
  `labour_expiry` date DEFAULT NULL,
  `labour_number` varchar(100) DEFAULT NULL,
  `chamber_file` varchar(255) DEFAULT NULL,
  `chamber_expiry` date DEFAULT NULL,
  `chamber_number` varchar(100) DEFAULT NULL,
  `insurance_file` varchar(255) DEFAULT NULL,
  `insurance_certificate_expiry` date DEFAULT NULL,
  `insurance_certificate_number` varchar(100) DEFAULT NULL,
  `moa_aoa_file` varchar(255) DEFAULT NULL,
  `board_resolution_file` varchar(255) DEFAULT NULL,
  `poa_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_documents_company` (`company_id`),
  CONSTRAINT `fk_documents_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sys_company_documents` (`id`, `company_id`, `establishment_file`, `establishment_expiry`, `establishment_number`, `immigration_file`, `immigration_expiry`, `immigration_number`, `labour_file`, `labour_expiry`, `labour_number`, `chamber_file`, `chamber_expiry`, `chamber_number`, `insurance_file`, `insurance_certificate_expiry`, `insurance_certificate_number`, `moa_aoa_file`, `board_resolution_file`, `poa_file`, `created_at`, `updated_at`) VALUES
(33,	280,	'uploads/company/docs/est_6930802309a45.jpg',	'2025-12-05',	'',	'uploads/company/docs/imm_693080230a4af.jpg',	'2025-12-07',	'',	'uploads/company/docs/lab_693080230ac70.jpg',	'2025-12-21',	'',	NULL,	NULL,	'',	NULL,	NULL,	'',	NULL,	NULL,	NULL,	'2025-12-03 17:51:54',	'2025-12-03 18:53:31');

DROP TABLE IF EXISTS `sys_company_hr_policies`;
CREATE TABLE `sys_company_hr_policies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `policy_date` date NOT NULL,
  `policy_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `policy_valid` date DEFAULT NULL,
  `view_to_employees` tinyint(1) NOT NULL DEFAULT '1',
  `policy_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `policy_details` mediumtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_hr_policies_company` (`company_id`),
  CONSTRAINT `fk_hr_policies_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sys_company_hr_policies` (`id`, `company_id`, `policy_date`, `policy_name`, `policy_category`, `policy_valid`, `view_to_employees`, `policy_file`, `policy_details`, `created_at`, `updated_at`) VALUES
(44,	280,	'2025-12-05',	'Leaves',	'health',	'2025-12-05',	1,	'uploads/company/hr_policies/policy_693071e4568c1.jpg',	'Leaeves policy',	'2025-12-03 17:52:44',	'2025-12-03 17:52:44'),
(45,	280,	'2025-12-05',	'Casual Leaves',	'health',	'2025-12-05',	1,	'uploads/company/hr_policies/policy_693073273cdeb.jpg',	'Casual policy',	'2025-12-03 17:58:07',	'2025-12-03 17:58:07');

DROP TABLE IF EXISTS `sys_company_people`;
CREATE TABLE `sys_company_people` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `type` enum('owner','sponsor','contact') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'owner = Company Owner, sponsor = Sponsor, contact = Contact Person',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Only for contact person',
  `passport_copy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emirates_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visa_copy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_type` (`type`),
  CONSTRAINT `fk_company_people_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sys_company_people` (`id`, `company_id`, `type`, `name`, `mobile`, `email`, `designation`, `passport_copy`, `emirates_id`, `visa_copy`, `created_at`, `updated_at`) VALUES
(751,	280,	'owner',	'1 st owner venus',	'9999898988',	'owner@gmail.com',	NULL,	'uploads/company/file_69308021aae93.jpg',	'uploads/company/file_69308021ac185.jpg',	'uploads/company/file_69308021acd51.jpg',	'2025-12-03 18:53:29',	'2025-12-03 18:53:29'),
(752,	280,	'owner',	'second owner',	'9898989898',	'second@gmail.com',	NULL,	'uploads/company/file_69308021b08ca.jpg',	NULL,	NULL,	'2025-12-03 18:53:29',	'2025-12-03 18:53:29'),
(753,	280,	'sponsor',	'sponsordetails venus',	'9898989888',	'sponser@gmail.com',	NULL,	'uploads/company/file_69308021b22ae.jpg',	'uploads/company/file_69308021b2c1f.jpg',	NULL,	'2025-12-03 18:53:29',	'2025-12-03 18:53:29'),
(754,	280,	'contact',	'contact person details venus',	'9998989899',	'contact@gmail.com',	'wewewe',	'uploads/company/file_69308021b50c7.jpg',	'uploads/company/file_69308021b5b0b.jpg',	NULL,	'2025-12-03 18:53:29',	'2025-12-03 18:53:29');

DROP TABLE IF EXISTS `sys_company_settings`;
CREATE TABLE `sys_company_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `is_customer_code` tinyint(1) DEFAULT '0',
  `is_supplier_code` tinyint(1) DEFAULT '0',
  `is_account_code` tinyint(1) DEFAULT '0',
  `is_subaccount_code` tinyint(1) DEFAULT '0',
  `currency` varchar(10) DEFAULT NULL,
  `currency_digit` tinyint(1) DEFAULT '0',
  `book_closed` date DEFAULT NULL,
  `sales_code` varchar(50) DEFAULT NULL,
  `other_code` varchar(50) DEFAULT NULL,
  `hr_wps_establishment_id` varchar(100) DEFAULT NULL,
  `hr_wps_bank` varchar(100) DEFAULT NULL,
  `hr_wps_salary_file_code` varchar(50) DEFAULT NULL,
  `hr_payroll_cycle` enum('monthly','bi-weekly','weekly') DEFAULT NULL,
  `hr_payroll_start` tinyint DEFAULT NULL,
  `hr_payroll_end` tinyint DEFAULT NULL,
  `hr_weekly_off` varchar(15) DEFAULT NULL,
  `hr_gratuity_method` enum('basic_salary','gross_salary') DEFAULT NULL,
  `hr_insurance_provider` varchar(100) DEFAULT NULL,
  `hr_insurance_policy_number` varchar(100) DEFAULT NULL,
  `hr_insurance_policy_expiry` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_company_settings_company` (`company_id`),
  CONSTRAINT `fk_company_settings_company` FOREIGN KEY (`company_id`) REFERENCES `sys_company` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sys_company_settings` (`id`, `company_id`, `is_customer_code`, `is_supplier_code`, `is_account_code`, `is_subaccount_code`, `currency`, `currency_digit`, `book_closed`, `sales_code`, `other_code`, `hr_wps_establishment_id`, `hr_wps_bank`, `hr_wps_salary_file_code`, `hr_payroll_cycle`, `hr_payroll_start`, `hr_payroll_end`, `hr_weekly_off`, `hr_gratuity_method`, `hr_insurance_provider`, `hr_insurance_policy_number`, `hr_insurance_policy_expiry`, `created_at`, `updated_at`) VALUES
(2,	280,	1,	1,	1,	1,	'AED',	1,	'2025-12-12',	'1',	'11',	'1122',	'Kotak',	'k933',	'monthly',	1,	30,	'sunday',	'basic_salary',	'11',	'22111',	'2025-12-05',	'2025-12-03 17:53:57',	'2025-12-03 17:53:57');

-- 2025-12-03 18:53:29
