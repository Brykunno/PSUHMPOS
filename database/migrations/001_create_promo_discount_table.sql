-- Create promo_discount table for promotional discounts
CREATE TABLE IF NOT EXISTS `promo_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `description` text,
  `discount_type` enum('percentage', 'fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10, 2) NOT NULL,
  `minimum_amount` decimal(10, 2) DEFAULT 0.00,
  `maximum_discount` decimal(10, 2) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_count` int(11) DEFAULT 0,
  `applicable_to` enum('all_items', 'category', 'menu_item') NOT NULL DEFAULT 'all_items',
  `applicable_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_flag` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
