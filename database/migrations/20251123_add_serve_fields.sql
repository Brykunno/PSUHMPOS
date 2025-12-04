-- Migration: add served tracking fields to order_items
ALTER TABLE `order_items`
    ADD COLUMN `served_by` INT(11) NULL DEFAULT NULL,
    ADD COLUMN `served_at` DATETIME NULL DEFAULT NULL;

-- Note: run this SQL in your database (phpMyAdmin or CLI).
