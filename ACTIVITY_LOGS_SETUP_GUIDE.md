# Activity Logs Feature - Setup Guide

## Overview
The Activity Logs feature allows administrators to track all user activities within the PSUHMPOS system. This includes logins, logouts, data creation, updates, and deletions.

## Installation Steps

### 1. Database Migration
Execute the SQL migration to create the activity_logs table:

```sql
-- Navigate to your phpMyAdmin or MySQL client
-- Select the 'psuhm_db' database
-- Execute the following SQL file:
database/migrations/activity_logs.sql
```

Or run this command in your terminal:
```bash
mysql -u root -p psuhm_db < database/migrations/activity_logs.sql
```

### 2. Verify Installation
After running the migration, verify that the `activity_logs` table has been created:
- Check phpMyAdmin for the new table
- The table should have the following columns:
  - id (Primary Key)
  - user_id (Foreign Key to users table)
  - action (varchar)
  - description (text)
  - ip_address (varchar)
  - user_agent (text)
  - module (varchar)
  - record_id (int)
  - date_created (datetime)

### 3. Access the Logs
1. Login as an administrator (type = 1)
2. Navigate to: **Maintenance > Activity Logs**
3. The logs page will display all user activities

## Features

### 1. Activity Tracking
The system automatically logs:
- **User Login/Logout** - When users log in or out of the system
- **Category Management** - Create, update, delete categories
- **Menu Management** - Create, update, delete menu items
- **User Management** - Create, update, delete user accounts
- **Order Operations** - Create, update orders (when integrated)
- **Discount Operations** - Create, update discounts (when integrated)

### 2. Filter Options
Filter logs by:
- User (specific user or all users)
- Action type (login, logout, create, update, delete, view, export)
- Module (users, orders, menus, categories, sales, etc.)
- Date range (from and to dates)
- Search term (search in description, action, or user name)

### 3. Statistics Dashboard
View activity statistics:
- Today's activities count
- This week's activities count
- This month's activities count
- Total logs count
- Most active users (last 30 days)
- Activity by module (last 30 days)

### 4. Export Functionality
- Export filtered logs to CSV format
- Includes all relevant information for auditing purposes

### 5. Log Details
View detailed information for each log entry:
- Log ID
- Date/Time
- User information (name, username, type)
- Action performed
- Module affected
- Record ID
- Description
- IP Address
- User Agent (browser/device information)

## Usage

### Viewing Logs
1. Go to **Maintenance > Activity Logs**
2. By default, all logs are displayed in descending order (newest first)
3. Use the DataTables search to quickly find specific entries

### Filtering Logs
1. Click the **Filters** button
2. Select your filter criteria:
   - User
   - Action type
   - Module
   - Date range
   - Search term
3. Click **Apply Filter**
4. Click **Reset** to clear all filters

### Viewing Log Details
1. Click the eye icon (👁️) on any log entry
2. A modal will display detailed information about that activity

### Exporting Logs
1. Apply filters if needed
2. Click the **Export** button
3. A CSV file will be downloaded with all filtered logs

## Security Considerations

### 1. Access Control
- Only administrators (user type = 1) can access the activity logs
- Non-admin users attempting to access will be redirected to 404 page

### 2. Data Retention
The ActivityLog class includes a cleanup function to delete old logs:
```php
// Delete logs older than 90 days (can be customized)
$activityLog->delete_old_logs(90);
```

You can set up a cron job or scheduled task to run this periodically.

### 3. Privacy
- Passwords are never logged
- IP addresses are stored for security auditing
- User agents are stored to identify the device/browser used

## Extending the Logs Feature

### Adding Logs to New Modules
To add activity logging to any new feature:

```php
// 1. Include the ActivityLog class
require_once('ActivityLog.php');

// 2. Create an instance
$activityLog = new ActivityLog();

// 3. Log the activity
$activityLog->log_activity(
    'action_type',           // 'create', 'update', 'delete', 'view', etc.
    'Description of action', // Human-readable description
    'module_name',           // e.g., 'orders', 'tables', 'reports'
    $record_id               // Optional: ID of affected record
);
```

### Example: Logging Order Creation
```php
// In your order creation function
if($save){
    $order_id = $this->conn->insert_id;
    
    // Log the activity
    $this->activityLog->log_activity(
        'create',
        "Created new order #$order_id for table $table_number",
        'orders',
        $order_id
    );
    
    // ... rest of your code
}
```

## Troubleshooting

### Logs Not Appearing
1. **Check Database**: Verify the activity_logs table exists
2. **Check Permissions**: Ensure you're logged in as admin (type = 1)
3. **Check Browser Console**: Look for JavaScript errors
4. **Check PHP Errors**: Enable error reporting to see any PHP issues

### Statistics Not Loading
1. **Check AJAX Request**: Open browser DevTools > Network tab
2. **Verify Endpoint**: Ensure `classes/ActivityLog.php?action=get_statistics` is accessible
3. **Check Database**: Verify there are logs in the activity_logs table

### Export Not Working
1. **Check File Permissions**: Ensure PHP can write temporary files
2. **Check Headers**: Verify no output before the export script runs
3. **Check Browser**: Some browsers may block downloads from certain sources

## Database Schema

```sql
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id_fk_al` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_module` (`module`),
  KEY `idx_date_created` (`date_created`),
  CONSTRAINT `user_id_fk_al` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

## Files Created/Modified

### New Files
- `database/migrations/activity_logs.sql` - Database migration
- `classes/ActivityLog.php` - Activity logging class
- `admin/logs/index.php` - Main logs interface
- `admin/logs/get_logs.php` - AJAX endpoint for fetching logs
- `admin/logs/view_log.php` - Log details modal
- `admin/logs/export_logs.php` - CSV export functionality
- `ACTIVITY_LOGS_SETUP_GUIDE.md` - This documentation

### Modified Files
- `classes/Login.php` - Added login/logout logging
- `classes/Master.php` - Added category and menu logging
- `classes/Users.php` - Added user management logging
- `admin/inc/navigation.php` - Added Activity Logs menu item

## Future Enhancements

Potential improvements for the activity logs feature:
1. Real-time log updates using WebSockets
2. Advanced analytics and charts
3. Email notifications for critical activities
4. Log archiving to external storage
5. Integration with SIEM systems
6. Custom alert rules
7. Scheduled reports via email
8. Log comparison and diff viewer

## Support

If you encounter any issues or need assistance:
1. Check the troubleshooting section above
2. Review the PHP error logs
3. Verify all files are properly uploaded
4. Ensure database permissions are correct

---

**Version**: 1.0  
**Created**: December 8, 2025  
**Author**: PSUHMPOS Development Team
