-- Add promo discount columns to order_list table
ALTER TABLE `order_list` 
ADD COLUMN `promo_discount_id` INT(30) DEFAULT NULL,
ADD COLUMN `promo_discount_amount` FLOAT(12,2) NOT NULL DEFAULT 0.00,
ADD CONSTRAINT `fk_order_promo` FOREIGN KEY (`promo_discount_id`) 
REFERENCES `promo_discount`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Create index for promo lookups
CREATE INDEX `idx_promo_discount_id` ON `order_list`(`promo_discount_id`);
