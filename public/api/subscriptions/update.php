<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/../../includes/Database.php';

use Includes\Database;

try {
    // Only allow PUT/PATCH requests
    if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'])) {
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

    // Validate subscription ID
    $subscriptionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$subscriptionId) {
        http_response_code(400); // Bad Request
        throw new Exception('Invalid subscription ID');
    }

    $db = Database::getInstance();

    // Verify subscription exists and belongs to user
    $existing = $db->fetchOne(
        "SELECT * FROM subscriptions WHERE id = ? AND active = 1",
        [$subscriptionId]
    );

    if (!$existing) {
        http_response_code(404); // Not Found
        throw new Exception('Subscription not found');
    }

    // Build update data
    $updateData = [];
    $validationErrors = [];
    
    // Validate content type if provided
    if (isset($data['content_type'])) {
        if (!in_array($data['content_type'], ['joke', 'quote'])) {
            $validationErrors[] = 'Invalid content type';
        } else {
            $updateData['content_type'] = $data['content_type'];
        }
    }

    // Validate delivery time if provided
    if (isset($data['delivery_time'])) {
        if (!preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/', $data['delivery_time'])) {
            $validationErrors[] = 'Invalid delivery time format';
        } else {
            $updateData['delivery_time'] = $data['delivery_time'];
        }
    }

    // Validate delivery method if provided
    if (isset($data['delivery_method'])) {
        if (!in_array($data['delivery_method'], ['email', 'sms'])) {
            $validationErrors[] = 'Invalid delivery method';
        } else {
            $updateData['delivery_method'] = $data['delivery_method'];
            
            // If changing to SMS, require phone number
            if ($data['delivery_method'] === 'sms' && 
                empty($existing['phone_number']) && 
                empty($data['phone_number'])) {
                $validationErrors[] = 'Phone number required for SMS delivery';
            }
        }
    }

    // If there are validation errors, throw exception
    if (!empty($validationErrors)) {
        http_response_code(422); // Unprocessable Entity
        throw new Exception(implode(', ', $validationErrors));
    }

    // Update optional fields
    if (isset($data['phone_number'])) {
        $updateData['phone_number'] = $data['phone_number'];
    }

    if (isset($data['active'])) {
        $updateData['active'] = (bool) $data['active'];
    }

    // If there are fields to update
    if (!empty($updateData)) {
        $db->update(
            'subscriptions',
            $updateData,
            'id = ?',
            [$subscriptionId]
        );
    }

    // Fetch updated subscription
    $subscription = $db->fetchOne(
        "SELECT * FROM subscriptions WHERE id = ?",
        [$subscriptionId]
    );

    http_response_code(200); // OK
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