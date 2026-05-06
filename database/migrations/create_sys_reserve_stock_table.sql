-- Migration for sys_reserve_stock table
-- This table stores reserve stock information from the Stock Register

CREATE TABLE `sys_reserve_stock` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `stock_id` INT UNSIGNED NOT NULL COMMENT 'Foreign key to sm_items.id (item/product ID)',
    `part_number` VARCHAR(255) NOT NULL COMMENT 'Part number for reference',
    `customer_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Customer name for reservation',
    `sales_person_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Foreign key to sm_staffs.id',
    `reserve_qty` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Quantity to reserve',
    `reserve_date` DATE NOT NULL COMMENT 'Date until which stock is reserved',
    `company_id` INT UNSIGNED NOT NULL COMMENT 'Foreign key to sys_company.id',
    `created_by` INT UNSIGNED NULL DEFAULT NULL COMMENT 'User who created the record',
    `updated_by` INT UNSIGNED NULL DEFAULT NULL COMMENT 'User who last updated the record',
    `deleted_by` INT UNSIGNED NULL DEFAULT NULL COMMENT 'User who deleted the record',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (`id`),
    INDEX `idx_stock_id` (`stock_id`),
    INDEX `idx_part_number` (`part_number`),
    INDEX `idx_sales_person_id` (`sales_person_id`),
    INDEX `idx_company_id` (`company_id`),
    INDEX `idx_reserve_date` (`reserve_date`),
    INDEX `idx_deleted_at` (`deleted_at`),
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table for storing reserved stock information';
