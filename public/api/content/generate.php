<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once dirname(dirname(dirname(__DIR__))) . '/bootstrap.php';
require_once dirname(dirname(dirname(__DIR__))) . '/includes/ContentGenerator.php';

use Includes\ContentGenerator;

try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        throw new Exception('Method not allowed');
    }

    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['type'])) {
        http_response_code(400); // Bad Request
        throw new Exception('Invalid request. Must specify content type');
    }

    // Initialize content generator
    $generator = new ContentGenerator($_ENV['OPENAI_API_KEY']);

    // Validate content type
    if (!in_array($data['type'], $generator->getSupportedTypes())) {
        http_response_code(422); // Unprocessable Entity
        throw new Exception('Invalid content type. Supported types: ' . implode(', ', $generator->getSupportedTypes()));
    }

    // Validate topic if provided
    $topic = null;
    if (isset($data['topic'])) {
        if (!$generator->supportsTopics($data['type'])) {
            throw new Exception("Content type '{$data['type']}' does not support topics");
        }
        $topic = filter_var($data['topic'], FILTER_SANITIZE_SPECIAL_CHARS);
    }

    // Generate content
    $content = $generator->generate($data['type'], $topic);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => [
            'type' => $data['type'],
            'topic' => $topic,
            'content' => $content['result'],
            'prompt' => $content['prompt'],
        ]
    ]);

} catch (Exception $e) {
    $code = http_response_code();
    // If no specific code was set, use 500
    if ($code === 200) {
        http_response_code(500);
    }
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}