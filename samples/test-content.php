<?php
echo "Current directory: " . __DIR__ . "\n";
echo "Looking for .env in: " . __DIR__ . "/../.env\n";

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../includes/ContentGenerator.php';

use Includes\ContentGenerator;

try {
    echo "Environment loaded. API Key: " . (empty($_ENV['OPENAI_API_KEY']) ? 'Not found' : 'Found') . "\n";
    
    // Create a new content generator instance
    $generator = new ContentGenerator($_ENV['OPENAI_API_KEY']);

    echo "Testing joke generation...\n";
    $joke = $generator->generateJoke();
    echo "Joke: " . $joke . "\n\n";

    echo "Testing quote generation...\n";
    $quote = $generator->generateQuote();
    echo "Quote: " . $quote . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}