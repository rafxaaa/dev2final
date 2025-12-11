<?php
/**
 * One-time script to set specific users as admins
 * Run this once, then delete it for security
 */

require 'config.php';

$admin_emails = [
    'avsagar@usc.edu',
    'rafaelv8@usc.edu',
    'daviddn@usc.edu',
    'ellenjun@usc.edu',
    'cstiker@usc.edu'
];

echo "Setting up admin users...\n\n";

$updated = 0;
$not_found = [];

foreach ($admin_emails as $email) {
    $stmt = $mysqli->prepare("UPDATE users SET security_level = 1 WHERE email = ?");
    $stmt->bind_param("s", $email);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "✓ Set $email as admin\n";
            $updated++;
        } else {
            echo "⚠ $email not found in database (user may not exist yet)\n";
            $not_found[] = $email;
        }
    } else {
        echo "✗ Error updating $email: " . $mysqli->error . "\n";
    }
    $stmt->close();
}

echo "\n";
echo "Summary:\n";
echo "- Updated: $updated users\n";
echo "- Not found: " . count($not_found) . " users\n";

if (!empty($not_found)) {
    echo "\nUsers not found (they may need to sign up first):\n";
    foreach ($not_found as $email) {
        echo "  - $email\n";
    }
}

echo "\nDone! You can now delete this file for security.\n";
?>

