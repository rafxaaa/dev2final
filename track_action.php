<?php
require 'config.php';

// Simple API endpoint to track user actions
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';
$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;

try {
    // Check if tables exist
    $table_check = $mysqli->query("SHOW TABLES LIKE 'route_requests'");
    if ($table_check->num_rows == 0) {
        // Tables don't exist yet - silently fail
        echo json_encode(['success' => true, 'note' => 'tables_not_setup']);
        exit;
    }
    
    switch ($action) {
        case 'route_request':
            $travel_mode = $_POST['travel_mode'] ?? 'driving';
            $route_type = $_POST['route_type'] ?? 'fastest';
            $start_lat = isset($_POST['start_lat']) ? floatval($_POST['start_lat']) : null;
            $start_lng = isset($_POST['start_lng']) ? floatval($_POST['start_lng']) : null;
            $end_lat = isset($_POST['end_lat']) ? floatval($_POST['end_lat']) : null;
            $end_lng = isset($_POST['end_lng']) ? floatval($_POST['end_lng']) : null;
            
            $stmt = $mysqli->prepare("
                INSERT INTO route_requests 
                (user_id, travel_mode, route_type, start_lat, start_lng, end_lat, end_lng)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            if ($stmt) {
                $stmt->bind_param("issdddd", $user_id, $travel_mode, $route_type, 
                                $start_lat, $start_lng, $end_lat, $end_lng);
                $stmt->execute();
                $stmt->close();
            }
            break;
            
        case 'search':
            $search_term = $_POST['search_term'] ?? null;
            $filter_offense = $_POST['filter_offense'] ?? null;
            $filter_disposition = $_POST['filter_disposition'] ?? null;
            $date_from = !empty($_POST['date_from']) ? $_POST['date_from'] : null;
            $date_to = !empty($_POST['date_to']) ? $_POST['date_to'] : null;
            
            $stmt = $mysqli->prepare("
                INSERT INTO search_queries 
                (user_id, search_term, filter_offense, filter_disposition, date_from, date_to)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            if ($stmt) {
                $stmt->bind_param("isssss", $user_id, $search_term, $filter_offense, 
                                $filter_disposition, $date_from, $date_to);
                $stmt->execute();
                $stmt->close();
            }
            break;
            
        case 'interaction':
            $interaction_type = $_POST['interaction_type'] ?? '';
            $details = $_POST['details'] ?? null;
            
            $stmt = $mysqli->prepare("
                INSERT INTO map_interactions (user_id, interaction_type, details)
                VALUES (?, ?, ?)
            ");
            if ($stmt) {
                $stmt->bind_param("iss", $user_id, $interaction_type, $details);
                $stmt->execute();
                $stmt->close();
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            exit;
    }
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}

