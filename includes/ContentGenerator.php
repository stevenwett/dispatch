<?php
namespace Includes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class ContentGenerator {
    private string $apiKey;
    private Client $client;
    private string $model = "gpt-3.5-turbo";

    // Define content types and their corresponding prompts
    private array $contentTypes = [
        'joke' => [
            'prompt' => "Tell me a short, clean, family-friendly joke%s. Respond with just the joke text, no explanations or additional context.",
            'supports_topic' => true
        ],
        'quote' => [
            'prompt' => "Share an inspiring quote. Include the author if known. Respond with just the quote and author, no additional context.",
            'supports_topic' => false
        ]
        // Add new content types here following the same pattern
        // 'riddle' => [
        //     'prompt' => "Create a clever riddle%s. Provide just the riddle text.",
        //     'supports_topic' => true
        // ],
        // 'fact' => [
        //     'prompt' => "Share an interesting fact%s. Be concise and direct.",
        //     'supports_topic' => true
        // ]
    ];

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
        $this->initializeClient();
    }

    private function initializeClient(): void {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Get list of supported content types
     */
    public function getSupportedTypes(): array {
        return array_keys($this->contentTypes);
    }

    /**
     * Check if content type supports topics
     */
    public function supportsTopics(string $type): bool {
        return isset($this->contentTypes[$type]) && $this->contentTypes[$type]['supports_topic'];
    }

    /**
     * Generate content of any supported type
     */
    public function generate(string $type, ?string $topic = null): string {
        if (!isset($this->contentTypes[$type])) {
            throw new Exception("Unsupported content type: $type");
        }

        $config = $this->contentTypes[$type];
        $topicPhrase = $topic && $config['supports_topic'] ? " about " . $topic : "";
        $prompt = sprintf($config['prompt'], $topicPhrase);
        
        return $this->generateContent($prompt);
    }

    private function generateContent(string $prompt): string {
        try {
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a helpful assistant that generates content. Be concise and direct.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 150,
                    'temperature' => 0.7
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            if (!isset($result['choices'][0]['message']['content'])) {
                throw new Exception('Unexpected API response format');
            }

            return trim($result['choices'][0]['message']['content']);

        } catch (GuzzleException $e) {
            error_log("OpenAI API Error: " . $e->getMessage());
            throw new Exception("Failed to generate content. Please try again later.");
        } catch (Exception $e) {
            error_log("Content Generation Error: " . $e->getMessage());
            throw new Exception("An error occurred while generating content.");
        }
    }

    public function setModel(string $model): void {
        $this->model = $model;
    }
}