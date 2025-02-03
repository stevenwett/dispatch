<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/Database.php';

use Includes\Database;

try {
    // Only allow DELETE requests
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405); // Method Not Allowed
        throw new Exception('Method not allowed');
    }

    // Validate subscription ID
    $subscriptionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$subscriptionId) {
        http_response_code(400); // Bad Request
        throw new Exception('Invalid subscription ID');
    }

    $db = Database::getInstance();

    // Verify subscription exists
    $subscription = $db->fetchOne(
        "SELECT * FROM subscriptions WHERE id = ? AND active = 1",
        [$subscriptionId]
    );

    if (!$subscription) {
        http_response_code(404); // Not Found
        throw new Exception('Subscription not found');
    }

    // Soft delete by setting active = false
    $db->update(
        'subscriptions',
        ['active' => false],
        'id = ?',
        [$subscriptionId]
    );

    http_response_code(204); // No Content
    exit; // Don't send any content with 204 response

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