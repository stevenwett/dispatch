<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/Database.php';

use Includes\Database;

try {
    // Only allow GET requests
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405); // Method Not Allowed
        throw new Exception('Method not allowed');
    }

    $db = Database::getInstance();
    
    // Check if specific subscription ID is requested
    $subscriptionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    // Get user_id from authenticated session (we'll add auth later)
    $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
    
    if (!$userId) {
        http_response_code(400); // Bad Request
        throw new Exception('Invalid user ID');
    }

    if ($subscriptionId) {
        // Fetch specific subscription
        $subscription = $db->fetchOne(
            "SELECT * FROM subscriptions WHERE id = ? AND user_id = ? AND active = 1",
            [$subscriptionId, $userId]
        );

        if (!$subscription) {
            http_response_code(404); // Not Found
            throw new Exception('Subscription not found');
        }

        $response = $subscription;
    } else {
        // Fetch all active subscriptions for user
        $subscriptions = $db->fetchAll(
            "SELECT * FROM subscriptions WHERE user_id = ? AND active = 1",
            [$userId]
        );

        $response = $subscriptions;
    }

    http_response_code(200); // OK
    echo json_encode([
        'success' => true,
        'data' => $response
    ]);

} catch (PDOException $e) {
    http_response_code(500); // Server Error
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    // HTTP status code already set in specific error cases
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}