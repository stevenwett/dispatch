<?php
require_once __DIR__ . '/../includes/ContentGenerator.php';
use Includes\ContentGenerator;

// Get 4-5 random topics for the placeholder
$allTopics = ContentGenerator::getDefaultTopics();
$randomKeys = array_rand($allTopics, 4);
$placeholderTopics = array_map(function($index) use ($allTopics) {
    return $allTopics[$index];
}, $randomKeys);
$placeholder = implode(', ', $placeholderTopics) . '...';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta Tags -->
    <title>AI Joke Generator - Get Fresh, Custom Jokes Instantly | Byte-sized Laughs</title>
    <meta name="title" content="AI Joke Generator - Get Fresh, Custom Jokes Instantly">
    <meta name="description" content="Generate clever, original jokes about any topic using AI. Our joke generator creates unique, customized humor instantly. Try it for free and get byte-sized laughs on demand!">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://dispatch.stevenwett.com/">
    <meta property="og:title" content="AI Joke Generator - Custom Jokes Instantly">
    <meta property="og:description" content="Generate clever, original jokes about any topic using AI. Get byte-sized laughs on demand!">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://dispatch.stevenwett.com/">
    <meta property="twitter:title" content="AI Joke Generator - Custom Jokes Instantly">
    <meta property="twitter:description" content="Generate clever, original jokes about any topic using AI. Get byte-sized laughs on demand!">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="keywords" content="joke generator, AI jokes, custom jokes, funny jokes, humor generator, comedy AI, joke maker">
    <meta name="author" content="Steven Wett">
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "AI Joke Generator",
        "description": "Generate clever, original jokes about any topic using AI technology.",
        "url": "https://dispatch.stevenwett.com",
        "applicationCategory": "Entertainment",
        "operatingSystem": "Web Browser",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        },
        "author": {
            "@type": "Person",
            "name": "Steven Wett"
        }
    }
    </script>

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <style>
    /* Smooth fade-in animation for content */
    @keyframes fade-in {
        from { 
            opacity: 0; 
            transform: translateY(20px);
            filter: blur(5px);
        }
        to { 
            opacity: 1; 
            transform: translateY(0);
            filter: blur(0);
        }
    }
    
    /* Bouncing dots animation */
    @keyframes bounce-dot {
        0%, 80%, 100% { 
            transform: scale(0.8);
            opacity: 0.5;
        }
        40% { 
            transform: scale(1.2);
            opacity: 1;
        }
    }

    /* Loading dots */
    .loading-dot {
        animation: bounce-dot 1.4s infinite ease-in-out both;
    }
    
    .loading-dot:nth-child(1) { animation-delay: -0.32s; }
    .loading-dot:nth-child(2) { animation-delay: -0.16s; }
    .loading-dot:nth-child(3) { animation-delay: 0s; }
    
    .animate-fade-in {
        animation: fade-in 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    /* Smooth transition for all elements */
    * {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen p-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Joke Generator</h1>
            <p class="text-gray-600">Byte-sized laughs</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl">            
            <!-- Topic Input -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">What should the joke be about?</label>
                <div class="relative">
                    <input type="search" id="topic" 
        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
        placeholder="Try <?php echo htmlspecialchars($placeholder); ?>">
                </div>
                <p class="mt-2 text-sm text-gray-500">Leave blank for a random joke</p>
            </div>
            
            <!-- Generate Button -->
            <button id="generate" 
                    class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition-all duration-300 ease-in-out cursor-pointer hover:shadow-lg">
                Make me laugh!
            </button>
            
            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden mt-8">
                <div class="flex items-center justify-center space-x-3">
                    <div class="loading-dot w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div class="loading-dot w-3 h-3 bg-blue-500 rounded-full"></div>
                    <div class="loading-dot w-3 h-3 bg-blue-500 rounded-full"></div>
                </div>
                <p class="text-center text-gray-600 mt-4 animate-fade-in">Coming up with something funny...</p>
            </div>

            <!-- Content Display -->
            <div id="contentDisplay" class="mt-8 hidden transform">
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div id="content" class="text-gray-800 text-lg leading-relaxed"></div>
                    <div id="error" class="text-red-500 mt-2"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-gray-500 text-sm">
                Made by Steven Wett &copy; <?php echo date('Y'); ?>
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const generateBtn = document.getElementById('generate');
            const topic = document.getElementById('topic');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const contentDisplay = document.getElementById('contentDisplay');
            const contentElement = document.getElementById('content');
            const errorElement = document.getElementById('error');

            // Function to generate joke
            async function generateJoke() {
                try {
                    // Update button state
                    generateBtn.disabled = true;
                    generateBtn.classList.add('opacity-75');
                    
                    // Show loading, hide previous content
                    loadingIndicator.classList.remove('hidden');
                    contentDisplay.classList.add('hidden');
                    errorElement.textContent = '';
                    
                    // Prepare request data
                    const requestData = {
                        type: 'joke'
                    };
                    
                    if (topic.value.trim()) {
                        requestData.topic = topic.value.trim();
                    }

                    // Make API request
                    const response = await fetch('/api/content/generate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(requestData)
                    });

                    const data = await response.json();

                    // Hide loading
                    loadingIndicator.classList.add('hidden');
                    contentDisplay.classList.remove('hidden');

                    if (data.success) {
                        contentElement.textContent = data.data.content;
                        contentDisplay.classList.add('animate-fade-in');
                    } else {
                        throw new Error(data.error || 'Failed to generate joke');
                    }

                } catch (error) {
                    loadingIndicator.classList.add('hidden');
                    contentDisplay.classList.remove('hidden');
                    errorElement.textContent = error.message;
                } finally {
                    // Reset button state
                    generateBtn.disabled = false;
                    generateBtn.classList.remove('opacity-75');
                }
            }

            // Listen for click on button
            generateBtn.addEventListener('click', generateJoke);

            // Listen for Enter key on input
            topic.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    generateJoke();
                }
            });
        });
    </script>
</body>
</html>