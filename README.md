# SafeRoute - Campus Safety Visualization

## Directory Structure

This SafeRoute application is contained in its own directory (`Dev2/`) and should be deployed separately from the main ACAD-276 folder structure. 

**Important:** When deploying, ensure this entire `Dev2/` directory is placed in its own web-accessible location (e.g., `/var/www/saferoute/` or similar), not nested within the main `acad276` folder structure.

## Setup Instructions

1. **Database Configuration**
   - Update `config.php` with your database credentials:
     - `$db_host` - Database host
     - `$db_user` - Database username
     - `$db_pass` - Database password
     - `$db_name` - Database name

2. **Database Schema**
   The application expects a `users` table with the following structure:
   ```sql
   CREATE TABLE users (
     user_id INT AUTO_INCREMENT PRIMARY KEY,
     full_name VARCHAR(255) NOT NULL,
     email VARCHAR(255) UNIQUE NOT NULL,
     password_hash VARCHAR(255) NOT NULL,
     security_level INT DEFAULT 0
   );
   ```
   
   **Security Levels:**
   - `0` = Regular user (default for new signups)
   - `1` = Admin (can access admin panel)

3. **Initial Admin Setup**
   - To create the first admin user, manually update the database:
     ```sql
     UPDATE users SET security_level = 1 WHERE email = 'admin@example.com';
     ```
   - Or sign up normally and then update via the admin panel (if you have another admin account)

## Features

- **User Authentication**: Login/Signup with session management
- **Dynamic Navigation**: Login/Logout buttons appear based on authentication status
- **Admin Panel**: User management for administrators (security level 1)
- **Interactive Map**: Crime and fire log visualization
- **Resources Page**: Safety information and contacts

## File Structure

```
Dev2/
├── config.php          # Database configuration and helper functions
├── login.php           # User login page
├── signup.php          # User registration page
├── logout.php          # Logout handler
├── admin.php           # Admin panel (requires security_level >= 1)
├── home.php            # Homepage
├── map2.php            # Interactive map page
├── about.php           # About page
├── resources.php       # Resources page
└── README.md           # This file
```

## Security Notes

- New users are automatically assigned `security_level = 0` (regular user)
- Only users with `security_level >= 1` can access the admin panel
- Admin users can:
  - View all users
  - Change user security levels
  - Delete users (except themselves)

