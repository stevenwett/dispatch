<?php

namespace Includes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class ContentGenerator {
    private string $apiKey;
    private Client $client;
    private string $model = "gpt-3.5-turbo";

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
        $this->initializeClient();
        
        // Initialize session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize used responses array in session if it doesn't exist
        if (!isset($_SESSION['used_responses'])) {
            $_SESSION['used_responses'] = [];
        }
    }

    // Make this public static so we can access it from the index page
    private static array $defaultTopics = [
        // Animals
        'cats', 'dogs', 'penguins', 'giraffes', 'pandas', 'elephants', 'sloths', 'dolphins', 'kangaroos', 'squirrels', 'zebras', 'koalas',
        // Food & Drinks
        'pizza', 'tacos', 'sushi', 'ice cream', 'chocolate', 'coffee', 'sandwiches', 'avocados', 'pasta', 'burgers', 'smoothies', 'donuts', 'bacon',
        // Daily Life
        'grocery shopping', 'working from home', 'traffic', 'alarm clocks', 'smartphones', 'laundry', 'dishes', 'meetings', 'commuting', 'naps',
        // Activities & Hobbies
        'yoga', 'gardening', 'cooking', 'hiking', 'dancing', 'gaming', 'painting', 'photography', 'karaoke', 'skateboarding', 'surfing', 'knitting',
        // Weather & Nature
        'rain', 'snow', 'sunshine', 'weather forecasts', 'seasons', 'clouds', 'rainbows', 'storms', 'wind', 'beach', 'mountains', 'forests',
        // Social & Events
        'social media', 'dating', 'family reunions', 'office life', 'parties', 'weddings', 'birthdays', 'graduations', 'job interviews', 'first dates',
        // Travel & Places
        'airports', 'vacations', 'road trips', 'tourist photos', 'hotels', 'camping', 'theme parks', 'trains', 'cruise ships', 'souvenirs',
        // Modern Life
        'wifi problems', 'streaming services', 'online shopping', 'selfies', 'autocorrect', 'video calls', 'emojis', 'hashtags', 'phone batteries',
        // Pop Culture
        'superheroes', 'movies', 'tv shows', 'viral videos', 'memes', 'celebrity gossip', 'fashion trends', 'tiktok dances', 'influencers',
        // Random & Fun
        'time travel', 'aliens', 'pirates', 'unicorns', 'ninjas', 'robots', 'zombies', 'dinosaurs', 'mermaids', 'wizards', 'ghosts',
        // Sports & Fitness
        'gym life', 'yoga pants', 'marathon training', 'sports fans', 'golf', 'swimming', 'basketball', 'exercise equipment', 'new year resolutions',
        // Seasonal
        'summer vacation', 'winter holidays', 'spring cleaning', 'fall fashion', 'halloween costumes', 'new years resolutions', 'valentines day',
        // Technology
        'smart homes', 'virtual reality', 'video games', 'social media', 'app updates', 'gadgets', 'tech support', 'passwords', 'voice assistants',
        // Work Life
        'monday mornings', 'zoom meetings', 'office coffee', 'deadlines', 'email chains', 'work dress codes', 'water cooler chat', 'team building'
    ];

    public function generate(string $type, ?string $topic = null): string {
        if (!isset($this->contentTypes[$type])) {
            throw new Exception("Unsupported content type: $type");
        }

        $config = $this->contentTypes[$type];
        
        // If no topic provided, randomly select one
        if (!$topic && $config['supports_topic']) {
            $topic = self::$defaultTopics[array_rand(self::$defaultTopics)];
        }

        $topicPhrase = $topic && $config['supports_topic'] ? " about " . $topic : "";
        $prompt = sprintf($config['prompt'], $topicPhrase);
        
        return $this->generateContent($prompt, $config['system_prompt'] ?? null);
    }

    private array $contentTypes = [
        'joke' => [
            'prompt' => "Create a clever, original joke%s. Avoid common formats. Just the joke text.",
            'supports_topic' => true,
            'system_prompt' => "You are a witty joke writer who creates original, unexpected humor. Never use common formats or clichés."
        ]
    ];

    // Add a static method to access the topics
    public static function getDefaultTopics(): array {
        return self::$defaultTopics;
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
                    'temperature' => 1.2,        // Increased for more randomness
                    'presence_penalty' => 1.0,   // Increased to strongly discourage repetition
                    'frequency_penalty' => 1.0,  // Increased to encourage unique word choices
                    'top_p' => 0.9              // Added to allow more creative token selection
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