<?php

namespace Includes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class ContentGenerator {
    private string $apiKey;
    private Client $client;
    private string $model = "gpt-3.5-turbo";
    private array $topicVariations = [
        'from the perspective of a confused alien',
        'as if explaining it to a 5-year-old',
        'in the style of a conspiracy theorist',
        'from the viewpoint of someone who has never seen/experienced it',
        'but set 100 years in the future',
        'as observed by a time traveler from the 1800s',
        'if it suddenly became sentient',
        'but everything goes hilariously wrong',
        'as interpreted by artificial intelligence',
        'but with an unexpected plot twist',
        'from the perspective of your grandmother',
        'if it happened in medieval times',
        'but it\'s actually a supervillain\'s secret plan',
        'as misunderstood by social media',
        'if it was featured in a dramatic documentary',
        'but it\'s secretly controlled by cats',
        'as experienced during a zombie apocalypse',
        'if it existed in a parallel universe',
        'but it\'s actually a elaborate marketing scheme',
        'as told by someone who\'s extremely enthusiastic about it',
        'if it was taught in a bizarre college course',
        'from the perspective of a demanding food critic',
        'but everyone is unnecessarily dramatic about it',
        'as misinterpreted by ancient historians',
        'if it was the plot of a soap opera',
        'but explained by a sleep-deprived scientist',
        'as if it were the greatest discovery in human history',
        'from the perspective of a disappointed parent',
        'but it\'s actually an elaborate heist plan',
        'as described in an overly detailed legal document',
        'if it was the subject of a passionate TED talk',
        'but it\'s being kept secret by the government',
        'as explained by someone who\'s really bad at keeping secrets',
        'from the perspective of a retired superhero',
        'but it\'s actually a misunderstanding between time travelers',
        'as interpreted by a group of gossiping squirrels',
        'if it was the main attraction at a theme park',
        'but it\'s been totally misrepresented in history books',
        'as explained by someone who thinks they\'re a spy',
        'from the perspective of a social media influencer having a meltdown',
        'if it was the subject of an intense rivalry between neighbors',
        'but it\'s actually controlled by a society of underground moles',
        'as described in an unnecessarily dramatic weather forecast',
        'from the perspective of a very confused ghost',
        'if it was the center of an intergalactic diplomatic incident',
        'but it\'s being debated by a panel of expert toddlers',
        'as explained by someone who learned about it through memes',
        'if it was discovered in an ancient tomb',
        'but it\'s actually a miscommunication between parallel universes',
        'as interpreted by a group of judgmental houseplants',
        'from the perspective of an overenthusiastic tour guide',
        'if it was the subject of a professional sports league',
        'but it\'s being discussed at a very serious board meeting',
        'as explained by someone who only communicates in emojis',
        'if it was the plot of a reality TV show gone wrong',
        'but it\'s actually a secret society of retired circus performers',
        'as described in a series of increasingly urgent text messages',
        'from the perspective of a time-traveling food blogger',
        'if it was the cause of an interspecies misunderstanding',
        'but it\'s being analyzed by overthinking philosophy majors',
        'as explained by someone who\'s clearly making it up as they go',
        'if it was the subject of an underground black market',
        'but it\'s actually a complex code used by secret agents',
        'as interpreted by a group of conspiracy theorist pigeons',
        'from the perspective of an ai learning human behavior',
        'if it was the focus of an unnecessarily intense debate',
        'but it\'s being explained by a stressed-out wedding planner',
        'as described by someone who only speaks in movie quotes',
        'if it was the subject of a forbidden ancient prophecy',
        'but it\'s actually an elaborate plan by intelligent dolphins',
    ];

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

    public function generate(string $type, ?string $topic = null): array {
        if (!isset($this->contentTypes[$type])) {
            throw new Exception("Unsupported content type: $type");
        }

        $config = $this->contentTypes[$type];
        
        // If no topic provided, randomly select one
        if (!$topic && $config['supports_topic']) {
            $topic = self::$defaultTopics[array_rand(self::$defaultTopics)];
        }

        // Randomly decide whether to add a variation (70% chance)
        $variation = '';
        if ($topic && $config['supports_topic']) {
            // 25% chance of getting 2 variations, 75% chance of 1 variation
            $numVariations = (rand(1, 100) <= 25) ? 2 : 1;
            
            $variations = array_rand($this->topicVariations, $numVariations);
            if (!is_array($variations)) {
                $variations = [$variations];
            }
            
            $selectedVariations = [];
            foreach ($variations as $index) {
                $selectedVariations[] = $this->topicVariations[$index];
            }
            
            $variation = implode(' and ', $selectedVariations);
        }

        $topicPhrase = $topic ? $topic . ', ' . $variation : "";
        $prompt = sprintf($config['prompt'], $topicPhrase, $variation ? " Include this perspective in the joke." : "");
        
        return [
            'prompt' => $prompt,
            'result' => $this->generateContent($prompt, $config['system_prompt'] ?? null),
        ];
    }

    private array $contentTypes = [
        'joke' => [
            'prompt' => "Create a clever, original joke with the topic: %s. Avoid common formats and don't use ladder jokes. Just the joke text.",
            'supports_topic' => true,
            'system_prompt' => "You are a witty joke writer who creates original, unexpected humor. Never use these jokes: common formats, clichés, ladders, hide-and-seek, or break up. Don't promote comspiracy theories."
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
                    'max_tokens' => 200,
                    'temperature' => 1.4,
                    'presence_penalty' => 1.5,   // Increased to strongly discourage repetition
                    'frequency_penalty' => 1.5,  // Increased to encourage unique word choices
                    'top_p' => 0.9               // Added to allow more creative token selection
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