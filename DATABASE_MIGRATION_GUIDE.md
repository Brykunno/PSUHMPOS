# Database Migration Guide - Promo Discount Integration

## Overview
This guide explains how to apply the database schema changes needed for promo discount support in orders.

## Migration File Location
- **File**: `database/migrations/002_add_promo_to_orders.sql`
- **Purpose**: Adds promo discount columns to order_list table

## What This Migration Does

### Changes Made to `order_list` Table

1. **New Column: `promo_discount_id`**
   - Type: INT(30)
   - Default: NULL
   - Purpose: Foreign key reference to promo_discount table
   - Allows: NULL (for orders without promos)

2. **New Column: `promo_discount_amount`**
   - Type: FLOAT(12,2)
   - Default: 0.00
   - Purpose: Stores the discount amount applied to order
   - Format: Currency with 2 decimal places

3. **Foreign Key Constraint: `fk_order_promo`**
   - Links: order_list.promo_discount_id → promo_discount.id
   - On Delete: SET NULL (preserve order history if promo deleted)
   - On Update: CASCADE (update references if promo ID changes)

4. **Index: `idx_promo_discount_id`**
   - Column: promo_discount_id
   - Purpose: Optimize queries filtering by promo discount
   - Improves: Report generation, analytics queries

## How to Apply the Migration

### Option 1: Using phpMyAdmin (Recommended for GUI Users)

1. Open phpMyAdmin in your browser
2. Navigate to your database (e.g., `psuhm_pos`)
3. Click "SQL" tab at the top
4. Open `database/migrations/002_add_promo_to_orders.sql` file
5. Copy the entire SQL content
6. Paste into phpMyAdmin SQL editor
7. Click "Go" to execute

### Option 2: Using MySQL Command Line

```bash
# Navigate to your project directory
cd C:\xampp\htdocs\PSUHMPOS

# Run the migration file
mysql -u root -p your_database_name < database/migrations/002_add_promo_to_orders.sql

# Enter password when prompted
```

### Option 3: Using MySQL Workbench

1. Open MySQL Workbench
2. Connect to your MySQL server
3. Go to File → Open SQL Script
4. Select `database/migrations/002_add_promo_to_orders.sql`
5. Review the SQL
6. Click "Execute" button

### Option 4: Manual SQL Execution

```sql
-- Run these commands in your database:

ALTER TABLE `order_list` 
ADD COLUMN `promo_discount_id` INT(30) DEFAULT NULL,
ADD COLUMN `promo_discount_amount` FLOAT(12,2) NOT NULL DEFAULT 0.00,
ADD CONSTRAINT `fk_order_promo` FOREIGN KEY (`promo_discount_id`) 
REFERENCES `promo_discount`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

CREATE INDEX `idx_promo_discount_id` ON `order_list`(`promo_discount_id`);
```

## Verification

### Check if Migration Applied Successfully

```sql
-- Show table structure
DESCRIBE order_list;

-- Look for these columns in the output:
-- promo_discount_id        | int(30)    | NULL
-- promo_discount_amount    | float(12,2)| NO

-- Check if index exists
SHOW INDEXES FROM order_list;

-- Look for idx_promo_discount_id in the output

-- Verify foreign key
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'order_list' AND COLUMN_NAME = 'promo_discount_id';
```

### Expected Output After Migration

```
Table: order_list

Columns:
- id (int)
- user_id (int)
- table_id (int)
- code (varchar)
- ... existing columns ...
- promo_discount_id (int) ← NEW
- promo_discount_amount (float) ← NEW

Indexes:
- PRIMARY (id)
- ... existing indexes ...
- idx_promo_discount_id (promo_discount_id) ← NEW

Foreign Keys:
- ... existing constraints ...
- fk_order_promo: promo_discount_id → promo_discount.id ← NEW
```

## Rollback (If Needed)

If you need to undo this migration:

```sql
-- Remove foreign key constraint
ALTER TABLE `order_list` DROP FOREIGN KEY `fk_order_promo`;

-- Remove index
DROP INDEX `idx_promo_discount_id` ON `order_list`;

-- Remove columns
ALTER TABLE `order_list` 
DROP COLUMN `promo_discount_id`,
DROP COLUMN `promo_discount_amount`;
```

## Troubleshooting

### Error: "Foreign key constraint is incorrectly formed"
- **Cause**: promo_discount table doesn't exist or has different structure
- **Solution**: Ensure `001_create_promo_discount_table.sql` was applied first

### Error: "Table 'column_name' doesn't exist"
- **Cause**: Column already exists from previous migration
- **Solution**: Check if columns already present (run DESCRIBE order_list)

### Error: "Duplicate index name"
- **Cause**: Index already exists from previous run
- **Solution**: Safe to re-run - will skip existing index

### Data Loss Warning
- **Important**: This migration only **adds** new columns
- **No existing data** will be deleted or modified
- **Backward compatible**: Orders without promos will have NULL/0.00 values

## Verification Script

Run this to verify everything is working:

```sql
-- Check table structure
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'order_list' 
AND COLUMN_NAME IN ('promo_discount_id', 'promo_discount_amount');

-- Expected result:
-- promo_discount_id        | int(30)    | YES   | NULL
-- promo_discount_amount    | float      | NO    | 0.00

-- Try inserting a test order (optional)
-- Should succeed without errors
INSERT INTO order_list (user_id, code, queue, total_amount, status, date_created, date_updated, order_type, discount_type)
VALUES (1, 'TEST001', '00001', 100.00, 0, NOW(), NOW(), 'Takeaway', 'N/A');

-- Verify columns accept promo data
UPDATE order_list SET promo_discount_id = 1, promo_discount_amount = 10.00 
WHERE code = 'TEST001';

-- Clean up test
DELETE FROM order_list WHERE code = 'TEST001';
```

## Next Steps

After applying this migration:

1. ✅ POS promo selector becomes fully functional
2. ✅ Orders can now store promo discount data
3. ✅ Usage tracking automatically works
4. ✅ Reports can filter by promo discounts
5. ✅ Cashiers can apply promos during checkout

## Support

If you encounter issues:
1. Check the error message carefully
2. Verify promo_discount table exists first
3. Ensure database user has ALTER TABLE privileges
4. Review database backups before running
5. Test in development environment first

---

**Migration Date**: Applied when POS integration is ready
**Dependency**: Requires `001_create_promo_discount_table.sql` to run first
**Backward Compatibility**: ✅ Fully compatible - no data loss
