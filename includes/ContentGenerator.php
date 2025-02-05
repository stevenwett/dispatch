<?php

namespace Includes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class ContentGenerator {
    private string $apiKey;
    private Client $client;
    private string $model = "gpt-3.5-turbo";

    private array $contentTypes = [
        'joke' => [
            'prompt' => "Create a clever, original joke%s. Avoid common formats. Just the joke text.",
            'supports_topic' => true,
            'system_prompt' => "You are a witty joke writer who creates original, unexpected humor. Never use common formats or clichés."
        ],
        'quote' => [
            'prompt' => "Share an inspiring quote with author.",
            'supports_topic' => false
        ]
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

    public function getSupportedTypes(): array {
        return array_keys($this->contentTypes);
    }

    public function supportsTopics(string $type): bool {
        return isset($this->contentTypes[$type]) && $this->contentTypes[$type]['supports_topic'];
    }

    public function generate(string $type, ?string $topic = null): string {
        if (!isset($this->contentTypes[$type])) {
            throw new Exception("Unsupported content type: $type");
        }

        $config = $this->contentTypes[$type];
        $topicPhrase = $topic && $config['supports_topic'] ? " about " . $topic : "";
        $prompt = sprintf($config['prompt'], $topicPhrase);
        
        return $this->generateContent($prompt, $config['system_prompt'] ?? null);
    }

    private function generateContent(string $prompt, ?string $systemPrompt = null): string {
        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => $systemPrompt ?? 'Be concise and direct.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];

            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => $messages,
                    'max_tokens' => 150,
                    'temperature' => 0.9,
                    'presence_penalty' => 0.6,
                    'frequency_penalty' => 0.6
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