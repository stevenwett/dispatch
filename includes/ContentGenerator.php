<?php

namespace Includes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class ContentGenerator {
    private string $apiKey;
    private Client $client;
    private string $model = 'gpt-4o-mini';
    private array $topicVariations = [
        [
            'perspective' => 'as explained in a corporate PowerPoint presentation',
            'speaker' => 'enthusiastic middle manager'
        ],
        [
            'perspective' => 'from the perspective of a nervous intern',
            'speaker' => 'nervous intern'
        ],
        [
            'perspective' => 'as discussed in a very serious board meeting',
            'speaker' => 'eager executive'
        ],
        [
            'perspective' => 'as written in a passive-aggressive office email',
            'speaker' => 'a disgruntled office worker'
        ],
        [
            'perspective' => 'as explained by an overworked manager',
            'speaker' => 'overworked manager'
        ],
        [
            'perspective' => 'during a team-building exercise gone wrong',
            'speaker' => 'frustrated facilitator'
        ],
        [
            'perspective' => 'as presented in an unnecessarily long meeting',
            'speaker' => 'long-winded presenter'
        ],
        [
            'perspective' => 'from the perspective of the office coffee machine',
            'speaker' => 'exhausted coffee machine'
        ],
        [
            'perspective' => 'as written in a company-wide memo',
            'speaker' => 'overzealous administrator'
        ],
        [
            'perspective' => 'as debated during a workplace conflict resolution',
            'speaker' => 'diplomatic mediator'
        ],
        [
            'perspective' => 'as analyzed by overthinking philosophy majors',
            'speaker' => 'overthinking philosopher'
        ],
        [
            'perspective' => 'as explained by a sleep-deprived scientist',
            'speaker' => 'sleep-deprived scientist'
        ],
        [
            'perspective' => 'from the perspective of a demanding food critic',
            'speaker' => 'meticulous food critic'
        ],
        [
            'perspective' => 'as described in an overly detailed legal document',
            'speaker' => 'precise legal expert'
        ],
        [
            'perspective' => 'if it was the subject of a passionate TED talk',
            'speaker' => 'enthusiastic speaker'
        ],
        [
            'perspective' => 'as documented by a confused anthropologist',
            'speaker' => 'puzzled anthropologist'
        ],
        [
            'perspective' => 'as studied in an unusual research paper',
            'speaker' => 'eccentric researcher'
        ],
        [
            'perspective' => 'from the perspective of a perfectionist chef',
            'speaker' => 'perfectionist chef'
        ],
        [
            'perspective' => 'as explained by a wilderness survival expert',
            'speaker' => 'rugged survivalist'
        ],
        [
            'perspective' => 'as observed by a very particular museum curator',
            'speaker' => 'meticulous curator'
        ],
        [
            'perspective' => 'if it was the plot of a soap opera',
            'speaker' => 'dramatic soap star'
        ],
        [
            'perspective' => 'as featured on a true crime podcast',
            'speaker' => 'intense podcast host'
        ],
        [
            'perspective' => 'as covered by local news at 11',
            'speaker' => 'earnest news anchor'
        ],
        [
            'perspective' => 'if it was a viral social media trend',
            'speaker' => 'enthusiastic influencer'
        ],
        [
            'perspective' => 'as explained through interpretive dance',
            'speaker' => 'expressive dancer'
        ],
        [
            'perspective' => 'as a documentary narrated by Morgan Freeman',
            'speaker' => 'mesmerizing narrator'
        ],
        [
            'perspective' => 'as depicted in a badly translated movie',
            'speaker' => 'confused translator'
        ],
        [
            'perspective' => 'if it was a competitive reality show',
            'speaker' => 'intense reality host'
        ],
        [
            'perspective' => 'as a morning talk show segment',
            'speaker' => 'upbeat morning host'
        ],
        [
            'perspective' => 'as reviewed by harsh movie critics',
            'speaker' => 'cynical film critic'
        ],
        [
            'perspective' => 'from the perspective of your grandmother trying to use technology',
            'speaker' => 'bewildered grandmother'
        ],
        [
            'perspective' => 'as told by someone who\'s extremely enthusiastic but confused',
            'speaker' => 'enthusiastic novice'
        ],
        [
            'perspective' => 'by someone who thinks they\'re the first to discover it',
            'speaker' => 'oblivious pioneer'
        ],
        [
            'perspective' => 'from the view of a very patient kindergarten teacher',
            'speaker' => 'patient teacher'
        ],
        [
            'perspective' => 'as explained by an overexcited tour guide',
            'speaker' => 'overexcited guide'
        ],
        [
            'perspective' => 'by someone who\'s clearly making it up as they go',
            'speaker' => 'improvisational expert'
        ],
        [
            'perspective' => 'as told by the world\'s worst storyteller',
            'speaker' => 'rambling storyteller'
        ],
        [
            'perspective' => 'from the perspective of a retired superhero',
            'speaker' => 'retired superhero'
        ],
        [
            'perspective' => 'as narrated by a conspiracy theorist',
            'speaker' => 'excitable theorist'
        ],
        [
            'perspective' => 'by someone who missed the point entirely',
            'speaker' => 'clueless observer'
        ],
        [
            'perspective' => 'as observed by a time traveler from the 1800s',
            'speaker' => 'astonished time traveler'
        ],
        [
            'perspective' => 'if it happened in medieval times',
            'speaker' => 'medieval knight'
        ],
        [
            'perspective' => 'but set 100 years in the future',
            'speaker' => 'future historian'
        ],
        [
            'perspective' => 'as misinterpreted by ancient historians',
            'speaker' => 'confused historian'
        ],
        [
            'perspective' => 'if it was discovered in an ancient tomb',
            'speaker' => 'excited archaeologist'
        ],
        [
            'perspective' => 'as explained by a Victorian-era etiquette expert',
            'speaker' => 'proper Victorian'
        ],
        [
            'perspective' => 'during the height of the disco era',
            'speaker' => 'groovy disco dancer'
        ],
        [
            'perspective' => 'in the style of a Renaissance artist',
            'speaker' => 'inspired artist'
        ],
        [
            'perspective' => 'during the first day of the internet',
            'speaker' => 'amazed internet pioneer'
        ],
        [
            'perspective' => 'as remembered by someone with terrible memory',
            'speaker' => 'forgetful narrator'
        ],
        [
            'perspective' => 'as interpreted by a group of gossiping squirrels',
            'speaker' => 'chatty squirrel'
        ],
        [
            'perspective' => 'but it\'s secretly controlled by cats',
            'speaker' => 'mysterious cat'
        ],
        [
            'perspective' => 'from the perspective of a very judgmental house plant',
            'speaker' => 'judgmental plant'
        ],
        [
            'perspective' => 'as understood by migratory birds',
            'speaker' => 'migrating bird'
        ],
        [
            'perspective' => 'as explained by a group of tired zoo animals',
            'speaker' => 'exhausted zoo animal'
        ],
        [
            'perspective' => 'from the viewpoint of city pigeons',
            'speaker' => 'street-smart pigeon'
        ],
        [
            'perspective' => 'as discussed by garden gnomes',
            'speaker' => 'grumpy garden gnome'
        ],
        [
            'perspective' => 'according to neighborhood dogs',
            'speaker' => 'observant dog'
        ],
        [
            'perspective' => 'as witnessed by confused penguins',
            'speaker' => 'bewildered penguin'
        ],
        [
            'perspective' => 'from the perspective of a dramatic butterfly',
            'speaker' => 'dramatic butterfly'
        ],
        [
            'perspective' => 'as explained in a series of confusing emojis',
            'speaker' => 'emoji enthusiast'
        ],
        [
            'perspective' => 'through an AI chatbot having an existential crisis',
            'speaker' => 'existential AI'
        ],
        [
            'perspective' => 'as a mindfulness meditation gone wrong',
            'speaker' => 'anxious meditation guide'
        ],
        [
            'perspective' => 'during a video call with bad internet',
            'speaker' => 'frozen video caller'
        ],
        [
            'perspective' => 'as a social media influencer\'s sponsored post',
            'speaker' => 'eager influencer'
        ],
        [
            'perspective' => 'through a dating app bio',
            'speaker' => 'optimistic dater'
        ],
        [
            'perspective' => 'as explained to tech support at 3 AM',
            'speaker' => 'tired tech supporter'
        ],
        [
            'perspective' => 'during a failed attempt at meal prep',
            'speaker' => 'frustrated chef'
        ],
        [
            'perspective' => 'as interpreted by a smart home device',
            'speaker' => 'confused AI assistant'
        ],
        [
            'perspective' => 'during a yoga class mishap',
            'speaker' => 'unbalanced yogi'
        ],
        [
            'perspective' => 'as an elaborate insurance claim',
            'speaker' => 'suspicious claims adjuster'
        ],
        [
            'perspective' => 'during an awkward family holiday dinner',
            'speaker' => 'uncomfortable relative'
        ],
        [
            'perspective' => 'as a misunderstood fortune cookie message',
            'speaker' => 'cryptic fortune writer'
        ],
        [
            'perspective' => 'during an airport security check',
            'speaker' => 'stern security agent'
        ],
        [
            'perspective' => 'as a mysterious crop circle pattern',
            'speaker' => 'excited UFO enthusiast'
        ],
        [
            'perspective' => 'during a wedding toast gone wrong',
            'speaker' => 'nervous best man'
        ],
        [
            'perspective' => 'as interpreted by an art gallery critic',
            'speaker' => 'pretentious art critic'
        ],
        [
            'perspective' => 'during a cooking show disaster',
            'speaker' => 'frazzled TV chef'
        ],
        [
            'perspective' => 'during a parent-teacher conference',
            'speaker' => 'exhausted teacher'
        ],
        [
            'perspective' => 'as explained by an eccentric professor',
            'speaker' => 'eccentric professor'
        ],
        [
            'perspective' => 'during a weather forecast gone poetic',
            'speaker' => 'dramatic meteorologist'
        ],
        [
            'perspective' => 'as a self-help book chapter',
            'speaker' => 'inspirational author'
        ],
        [
            'perspective' => 'through an astronaut\'s mission log',
            'speaker' => 'space-bound astronaut'
        ],
        [
            'perspective' => 'as a archaeological discovery',
            'speaker' => 'excited archaeologist'
        ],
        [
            'perspective' => 'by a librarian having a rough day',
            'speaker' => 'irritated librarian'
        ],
        [
            'perspective' => 'during a political campaign speech',
            'speaker' => 'enthusiastic candidate'
        ],
        [
            'perspective' => 'as a motivational speaker losing motivation',
            'speaker' => 'dejected motivator'
        ],
        [
            'perspective' => 'through a celebrity\'s autobiography',
            'speaker' => 'dramatic celebrity'
        ],
        [
            'perspective' => 'as a scientific breakthrough announcement',
            'speaker' => 'excited scientist'
        ],
        [
            'perspective' => 'as a superhero origin story',
            'speaker' => 'nervous superhero'
        ],
        [
            'perspective' => 'through interpretive modern dance',
            'speaker' => 'interpretive dancer'
        ],
        [
            'perspective' => 'as a Broadway musical number',
            'speaker' => 'theatrical performer'
        ],
        [
            'perspective' => 'during a spelling bee competition',
            'speaker' => 'anxious contestant'
        ],
        [
            'perspective' => 'as a escape room puzzle',
            'speaker' => 'mysterious gamemaster'
        ],
        [
            'perspective' => 'through an unboxing video',
            'speaker' => 'excited reviewer'
        ],
        [
            'perspective' => 'as a medieval tavern tale',
            'speaker' => 'drunken bard'
        ],
        [
            'perspective' => 'during a magic trick explanation',
            'speaker' => 'mysterious magician'
        ],
        [
            'perspective' => 'as a nature documentary',
            'speaker' => 'whispered narrator'
        ],
        [
            'perspective' => 'through a choose-your-own-adventure story',
            'speaker' => 'indecisive narrator'
        ],
        [
            'perspective' => 'during a GPS recalculation',
            'speaker' => 'impatient navigator'
        ],
        [
            'perspective' => 'as assembly instructions',
            'speaker' => 'meticulous instruction writer'
        ],
        [
            'perspective' => 'through a customer service call',
            'speaker' => 'patient service rep'
        ],
        [
            'perspective' => 'as a grocery store announcement',
            'speaker' => 'monotone announcer'
        ],
        [
            'perspective' => 'during a gym workout routine',
            'speaker' => 'energetic trainer'
        ],
        [
            'perspective' => 'through a pet training session',
            'speaker' => 'patient pet trainer'
        ],
        [
            'perspective' => 'as a recipe blog post introduction',
            'speaker' => 'verbose food blogger'
        ],
        [
            'perspective' => 'during a laundromat adventure',
            'speaker' => 'frustrated customer'
        ],
        [
            'perspective' => 'as a parking ticket appeal',
            'speaker' => 'desperate driver'
        ],
        [
            'perspective' => 'through an elevator small talk',
            'speaker' => 'awkward stranger'
        ],
        [
            'perspective' => 'as performed by a street mime',
            'speaker' => 'silent mime'
        ],
        [
            'perspective' => 'during a karaoke night gone wrong',
            'speaker' => 'enthusiastic singer'
        ],
        [
            'perspective' => 'as explained by someone who just woke up',
            'speaker' => 'groggy sleeper'
        ],
        [
            'perspective' => 'through a series of passive-aggressive post-it notes',
            'speaker' => 'passive-aggressive roommate'
        ],
        [
            'perspective' => 'as interpreted by a bored museum security guard',
            'speaker' => 'bored security guard'
        ],
        [
            'perspective' => 'during a couples\' dance lesson',
            'speaker' => 'patient dance instructor'
        ],
        [
            'perspective' => 'as told by an overly competitive parent',
            'speaker' => 'intense soccer parent'
        ],
        [
            'perspective' => 'through a series of emergency texts',
            'speaker' => 'panicked texter'
        ],
        [
            'perspective' => 'as explained by someone who skimmed the wikipedia article',
            'speaker' => 'superficial expert'
        ],
        [
            'perspective' => 'during an awkward blind date',
            'speaker' => 'nervous dater'
        ],
        [
            'perspective' => 'as presented by a substitute teacher',
            'speaker' => 'unprepared teacher'
        ],
        [
            'perspective' => 'through a series of workplace safety videos',
            'speaker' => 'concerned safety officer'
        ],
        [
            'perspective' => 'during a children\'s birthday party chaos',
            'speaker' => 'overwhelmed party planner'
        ],
        [
            'perspective' => 'as interpreted by a fortune teller with poor reception',
            'speaker' => 'static-filled psychic'
        ],
        [
            'perspective' => 'through an IKEA instruction manual',
            'speaker' => 'minimalist illustrator'
        ],
        [
            'perspective' => 'as explained by someone stuck in an elevator',
            'speaker' => 'trapped elevator rider'
        ],
        [
            'perspective' => 'during a black friday shopping spree',
            'speaker' => 'frenzied shopper'
        ],
        [
            'perspective' => 'as understood by alien anthropologists',
            'speaker' => 'puzzled alien researcher'
        ],
        [
            'perspective' => 'through a series of angry yelp reviews',
            'speaker' => 'angry reviewer'
        ],
        [
            'perspective' => 'as explained by a sleep-talking roommate',
            'speaker' => 'mumbling sleeptalker'
        ],
        [
            'perspective' => 'during a high school reunion',
            'speaker' => 'nostalgic alumnus'
        ],
        [
            'perspective' => 'as interpreted by a method actor taking it too seriously',
            'speaker' => 'intense method actor'
        ],
        [
            'perspective' => 'through an overly detailed personality test',
            'speaker' => 'meticulous psychologist'
        ],
        [
            'perspective' => 'as explained by someone pretending to be an expert',
            'speaker' => 'confident amateur'
        ],
        [
            'perspective' => 'during a neighborhood watch meeting',
            'speaker' => 'suspicious neighbor'
        ],
        [
            'perspective' => 'through a series of chain emails',
            'speaker' => 'urgent forwarder'
        ],
        [
            'perspective' => 'as explained by someone trying to sound important',
            'speaker' => 'self-important expert'
        ],
        [
            'perspective' => 'during an open house tour',
            'speaker' => 'eager realtor'
        ],
        [
            'perspective' => 'as interpreted by a coffee-deprived barista',
            'speaker' => 'caffeine-deprived barista'
        ],
        [
            'perspective' => 'through a series of autocorrect mistakes',
            'speaker' => 'hasty texter'
        ],
        [
            'perspective' => 'as explained by someone in the wrong meeting',
            'speaker' => 'confused participant'
        ],
        [
            'perspective' => 'during a mindfulness retreat breakdown',
            'speaker' => 'anxious meditator'
        ],
        [
            'perspective' => 'as interpreted by a royal court jester',
            'speaker' => 'mischievous jester'
        ],
        [
            'perspective' => 'through a series of prophecies that keep getting revised',
            'speaker' => 'uncertain prophet'
        ],
        [
            'perspective' => 'as explained by someone who thinks they\'re whispering',
            'speaker' => 'loud whisperer'
        ],
        [
            'perspective' => 'during an emergency drill gone wrong',
            'speaker' => 'panicked coordinator'
        ],
        [
            'perspective' => 'as interpreted by a tired zoo keeper',
            'speaker' => 'exhausted zookeeper'
        ],
        [
            'perspective' => 'through a series of dad jokes',
            'speaker' => 'enthusiastic dad'
        ]
    ];

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
        $this->model = $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini';
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

    private function initializeShuffleBag(): void {
        if (!isset($_SESSION['variation_bag']) || empty($_SESSION['variation_bag'])) {
            $_SESSION['variation_bag'] = array_keys($this->topicVariations);
            shuffle($_SESSION['variation_bag']);
        }
    }
    
    private function getNextVariation(): ?array {
        $this->initializeShuffleBag();
        $index = array_pop($_SESSION['variation_bag']);
        
        return $this->topicVariations[$index] ?? null;
    }
    

    public function generate(string $type, ?string $topic = null): array {
        if (!isset($this->contentTypes[$type])) {
            throw new Exception("Unsupported content type: $type");
        }

        $config = $this->contentTypes[$type];
        
        // If no topic provided, randomly select one
        if (!$topic && $config['supports_topic']) {
            $topic = self::$defaultTopics[array_rand(self::$defaultTopics)];
        }

        // % chance to add a variation
        $useVariation = (mt_rand(1, 100) <= 40);

        $topicPhrase = $topic;
        if ($topic && $useVariation) {
            $variation = $this->getNextVariation();
            if ($variation) {
                $topicPhrase = $topic . ', ' . $variation['perspective'];
            }
        }

        $prompt = sprintf($config['prompt'], $topicPhrase);

        return [
            'prompt' => $prompt,
            'result' => $this->generateContent($prompt, $config['system_prompt'] ?? null),
            'variation_speaker' => $variation['speaker'] ?? '',
        ];
    }

    private array $contentTypes = [
        'joke' => [
            'prompt' => "Create an original joke with clever wordplay for the topic: %s. Just the joke text.",
            'supports_topic' => true,
            'system_prompt' => "You are a clever comedy writer specializing in original, witty humor. Your jokes should:

                - Use clever wordplay, unexpected connections, or absurd observations
                - Avoid obvious punchlines - the best jokes subvert expectations
                - Keep a lighthearted, playful tone
                - Be self-contained and work as a single joke
                - Adapt naturally to the given perspective or style variation

                Never use:
                - Generic formats (knock-knock, bar jokes, 'why did X cross the road')
                - Common joke structures (ladder jokes, hide-and-seek jokes)
                - Offensive stereotypes or mean-spirited humor
                - Inside jokes or references that require specific knowledge
                - Puns that rely on common wordplay
                - Meta-jokes about joke telling
                - Conspiracy theories or misinformation

                When given a perspective variation (like 'as explained by a tired scientist'):
                - Incorporate the perspective's voice and viewpoint naturally
                - Use relevant vocabulary and speech patterns
                - Let the perspective shape how the joke is told, not just what is told

                Keep jokes concise and punchy - aim for maximum impact with minimal setup."
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
                    'max_tokens' => 160,
                    'temperature' => 1.6,
                    'presence_penalty' => 1.8,   // Increased to strongly discourage repetition
                    'frequency_penalty' => 1.8,  // Increased to encourage unique word choices
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