-- SQL script to set specific users as admins (security_level = 1)
-- Run this in your database management tool (phpMyAdmin, MySQL Workbench, etc.)

UPDATE users SET security_level = 1 WHERE email = 'avsagar@usc.edu';
UPDATE users SET security_level = 1 WHERE email = 'rafaelv8@usc.edu';
UPDATE users SET security_level = 1 WHERE email = 'daviddn@usc.edu';
UPDATE users SET security_level = 1 WHERE email = 'ellenjun@usc.edu';
UPDATE users SET security_level = 1 WHERE email = 'cstiker@usc.edu';

-- Verify the updates
SELECT email, full_name, security_level 
FROM users 
WHERE email IN (
    'avsagar@usc.edu',
    'rafaelv8@usc.edu',
    'daviddn@usc.edu',
    'ellenjun@usc.edu',
    'cstiker@usc.edu'
);

