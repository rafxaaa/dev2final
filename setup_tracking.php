<?php
require 'config.php';

// Create tracking tables if they don't exist
$sql = "
CREATE TABLE IF NOT EXISTS route_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    travel_mode ENUM('driving', 'walking') NOT NULL,
    route_type ENUM('fastest', 'safe') NOT NULL,
    start_lat DECIMAL(10, 8) NULL,
    start_lng DECIMAL(11, 8) NULL,
    end_lat DECIMAL(10, 8) NULL,
    end_lng DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_travel_mode (travel_mode),
    INDEX idx_route_type (route_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS search_queries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    search_term VARCHAR(255) NULL,
    filter_offense VARCHAR(255) NULL,
    filter_disposition VARCHAR(255) NULL,
    date_from DATE NULL,
    date_to DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS map_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    interaction_type ENUM('filter_applied', 'marker_clicked', 'map_viewed') NOT NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_interaction_type (interaction_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Split by semicolon and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        if ($mysqli->query($statement)) {
            echo "✓ Table created/verified successfully<br>";
        } else {
            echo "✗ Error: " . $mysqli->error . "<br>";
        }
    }
}

echo "<br><strong>Setup complete!</strong> You can now delete this file for security.";
?>

