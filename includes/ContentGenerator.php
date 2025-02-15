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
        'as explained in a corporate PowerPoint presentation',
        'from the perspective of a nervous intern',
        'as discussed in a very serious board meeting',
        'as written in a passive-aggressive office email',
        'as explained by an overworked manager',
        'during a team-building exercise gone wrong',
        'as presented in an unnecessarily long meeting',
        'from the perspective of the office coffee machine',
        'as written in a company-wide memo',
        'as debated during a workplace conflict resolution',
        'as analyzed by overthinking philosophy majors',
        'as explained by a sleep-deprived scientist',
        'from the perspective of a demanding food critic',
        'as described in an overly detailed legal document',
        'if it was the subject of a passionate TED talk',
        'as documented by a confused anthropologist',
        'as studied in an unusual research paper',
        'from the perspective of a perfectionist chef',
        'as explained by a wilderness survival expert',
        'as observed by a very particular museum curator',
        'if it was the plot of a soap opera',
        'as featured on a true crime podcast',
        'as covered by local news at 11',
        'if it was a viral social media trend',
        'as explained through interpretive dance',
        'as a documentary narrated by Morgan Freeman',
        'as depicted in a badly translated movie',
        'if it was a competitive reality show',
        'as a morning talk show segment',
        'as reviewed by harsh movie critics',
        'from the perspective of your grandmother trying to use technology',
        'as told by someone who\'s extremely enthusiastic but confused',
        'by someone who thinks they\'re the first to discover it',
        'from the view of a very patient kindergarten teacher',
        'as explained by an overexcited tour guide',
        'by someone who\'s clearly making it up as they go',
        'as told by the world\'s worst storyteller',
        'from the perspective of a retired superhero',
        'as narrated by a conspiracy theorist',
        'by someone who missed the point entirely',
        'as observed by a time traveler from the 1800s',
        'if it happened in medieval times',
        'but set 100 years in the future',
        'as misinterpreted by ancient historians',
        'if it was discovered in an ancient tomb',
        'as explained by a Victorian-era etiquette expert',
        'during the height of the disco era',
        'in the style of a Renaissance artist',
        'during the first day of the internet',
        'as remembered by someone with terrible memory',
        'as interpreted by a group of gossiping squirrels',
        'but it\'s secretly controlled by cats',
        'from the perspective of a very judgmental house plant',
        'as understood by migratory birds',
        'as explained by a group of tired zoo animals',
        'from the viewpoint of city pigeons',
        'as discussed by garden gnomes',
        'according to neighborhood dogs',
        'as witnessed by confused penguins',
        'from the perspective of a dramatic butterfly',
        'as explained in a series of confusing emojis',
        'through an AI chatbot having an existential crisis',
        'as a mindfulness meditation gone wrong',
        'during a video call with bad internet',
        'as a social media influencer\'s sponsored post',
        'through a dating app bio',
        'as explained to tech support at 3 AM',
        'during a failed attempt at meal prep',
        'as interpreted by a smart home device',
        'during a yoga class mishap',
        'as an elaborate insurance claim',
        'during an awkward family holiday dinner',
        'as a misunderstood fortune cookie message',
        'during an airport security check',
        'as a mysterious crop circle pattern',
        'during a wedding toast gone wrong',
        'as interpreted by an art gallery critic',
        'during a cooking show disaster',
        'as a midnight infomercial pitch',
        'during a parent-teacher conference',
        'as explained by an eccentric professor',
        'during a weather forecast gone poetic',
        'as a self-help book chapter',
        'through an astronaut\'s mission log',
        'as a archaeological discovery',
        'by a librarian having a rough day',
        'during a political campaign speech',
        'as a motivational speaker losing motivation',
        'through a celebrity\'s autobiography',
        'as a scientific breakthrough announcement',
        'as a superhero origin story',
        'through interpretive modern dance',
        'as a Broadway musical number',
        'during a spelling bee competition',
        'as a escape room puzzle',
        'through an unboxing video',
        'as a medieval tavern tale',
        'during a magic trick explanation',
        'as a nature documentary',
        'through a choose-your-own-adventure story',
        'during a GPS recalculation',
        'as assembly instructions',
        'through a customer service call',
        'as a grocery store announcement',
        'during a gym workout routine',
        'through a pet training session',
        'as a recipe blog post introduction',
        'during a laundromat adventure',
        'as a parking ticket appeal',
        'through an elevator small talk',
        'as performed by a street mime',
        'during a karaoke night gone wrong',
        'as explained by someone who just woke up',
        'through a series of passive-aggressive post-it notes',
        'as interpreted by a bored museum security guard',
        'during a couples\' dance lesson',
        'as told by an overly competitive parent',
        'through a series of emergency texts',
        'as explained by someone who skimmed the wikipedia article',
        'during an awkward blind date',
        'as presented by a substitute teacher',
        'through a series of workplace safety videos',
        'as explained by someone who lost their voice',
        'during a children\'s birthday party chaos',
        'as interpreted by a fortune teller with poor reception',
        'through an IKEA instruction manual',
        'as explained by someone stuck in an elevator',
        'during a black friday shopping spree',
        'as understood by alien anthropologists',
        'through a series of angry yelp reviews',
        'as explained by a sleep-talking roommate',
        'during a high school reunion',
        'as interpreted by a method actor taking it too seriously',
        'through an overly detailed personality test',
        'as explained by someone pretending to be an expert',
        'during a neighborhood watch meeting',
        'as interpreted by an enthusiastic but wrong translator',
        'through a series of missed connections ads',
        'as explained by someone who\'s been awake for 48 hours',
        'during a home shopping network presentation',
        'as interpreted by a struggling psychic',
        'through a series of chain emails',
        'as explained by someone trying to sound important',
        'during an open house tour',
        'as interpreted by a coffee-deprived barista',
        'through a series of autocorrect mistakes',
        'as explained by someone in the wrong meeting',
        'during a mindfulness retreat breakdown',
        'as interpreted by a royal court jester',
        'through a series of prophecies that keep getting revised',
        'as explained by someone who thinks they\'re whispering',
        'during an emergency drill gone wrong',
        'as interpreted by a tired zoo keeper',
        'through a series of dad jokes',
        'as explained by someone trying to multitask',
        'during a speed dating session',
        'as interpreted by a haunted house actor',
        'through a series of meditation prompts',
        'as explained by someone who just learned about it',
        'during a stand-up comedy open mic',
        'as interpreted by a stressed wedding planner',
        'through a series of live news updates',
        'as explained by someone afraid of public speaking',
        'during a museum audio tour',
        'as interpreted by a confused time traveler',
        'through a series of spam emails',
        'as explained by someone reading from badly smudged notes',
        'during a family photo session',
        'as interpreted by a sleepwalking tour guide',
        'through a series of movie trailer voice-overs',
        'as explained by someone who lost their glasses',
        'during an improv comedy scene',
        'as interpreted by a retired circus performer',
        'through a series of ancient scrolls',
        'as explained by someone with hiccups',
        'during a talent show audition',
        'as interpreted by a professional whistler',
        'through a series of morse code messages',
        'as explained by someone in a rush',
        'during a medieval feast',
        'as interpreted by a child pretending to be an adult',
        'through a series of football play diagrams',
        'as explained by someone trying to break a world record',
        'during a ghost hunting expedition',
        'as interpreted by a mall santa on break',
        'through a series of interpretive whale songs',
        'as explained by someone who just finished a marathon',
        'during a silent disco',
        'as interpreted by a confused time period reenactor',
        'through a series of airline safety demonstrations',
        'as explained by someone practicing ventriloquism',
        'during a book club discussion',
        'as interpreted by a grumpy crossing guard',
        'through a series of hand puppet shows',
        'as explained by someone in zero gravity',
        'during a renaissance faire',
        'as interpreted by an overenthusiastic weatherperson',
        'through a series of wrong number texts',
        'as explained by someone trying to set a world record',
        'during a children\'s puppet show',
        'as interpreted by a frustrated driving instructor',
        'through a series of skywriting attempts',
        'as explained by someone speaking only in questions',
        'during a craft fair demonstration',
        'as interpreted by an eager student teacher',
        'through a series of fortune cookies',
        'as explained by someone trapped in a time loop',
        'during a drum circle',
        'as interpreted by an excited archeologist',
        'through a series of viral TikTok trends',
        'as explained by someone on a sugar rush',
        'during a charity auction',
        'as interpreted by a retired soap opera star',
        'through a series of misheard song lyrics',
        'as explained by someone trying to break bad news gently'
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

    private function initializeShuffleBag(): void {
        if (!isset($_SESSION['variation_bag']) || empty($_SESSION['variation_bag'])) {
            $_SESSION['variation_bag'] = array_keys($this->topicVariations);
            shuffle($_SESSION['variation_bag']);
        }
    }
    
    private function getNextVariation(): string {
        $this->initializeShuffleBag();
        $index = array_pop($_SESSION['variation_bag']);
        return $this->topicVariations[$index];
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

        // Randomly decide whether to add a variation (70% chance)
        $variation = $this->getNextVariation();

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