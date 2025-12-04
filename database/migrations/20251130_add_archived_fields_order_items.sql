-- Migration: add archived fields to order_items
ALTER TABLE `order_items`
  ADD COLUMN `archived` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN `archived_by` int(11) DEFAULT NULL,
  ADD COLUMN `archived_at` datetime DEFAULT NULL;
