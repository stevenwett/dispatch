<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/Database.php';

use Includes\Database;

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        throw new Exception('Method not allowed');
    }

    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        http_response_code(400); // Bad Request
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    $requiredFields = ['user_id', 'content_type', 'delivery_time', 'delivery_method'];
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        http_response_code(422); // Unprocessable Entity
        throw new Exception("Missing required fields: " . implode(', ', $missingFields));
    }

    // Validate content type
    if (!in_array($data['content_type'], ['joke', 'quote'])) {
        http_response_code(422);
        throw new Exception('Invalid content type');
    }

    // Validate delivery method
    if (!in_array($data['delivery_method'], ['email', 'sms'])) {
        http_response_code(422);
        throw new Exception('Invalid delivery method');
    }

    // Validate delivery time format (HH:MM:SS)
    if (!preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/', $data['delivery_time'])) {
        http_response_code(422);
        throw new Exception('Invalid delivery time format');
    }

    // If SMS delivery method, validate phone number
    if ($data['delivery_method'] === 'sms' && (!isset($data['phone_number']) || empty($data['phone_number']))) {
        http_response_code(422);
        throw new Exception('Phone number required for SMS delivery');
    }

    $db = Database::getInstance();

    // Insert subscription
    $subscriptionId = $db->insert('subscriptions', [
        'user_id' => $data['user_id'],
        'content_type' => $data['content_type'],
        'delivery_time' => $data['delivery_time'],
        'delivery_method' => $data['delivery_method'],
        'phone_number' => $data['phone_number'] ?? null,
        'active' => true
    ]);

    // Fetch the created subscription
    $subscription = $db->fetchOne(
        "SELECT * FROM subscriptions WHERE id = ?",
        [$subscriptionId]
    );

    http_response_code(201); // Created
    echo json_encode([
        'success' => true,
        'data' => $subscription
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