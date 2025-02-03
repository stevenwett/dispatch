<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Generator</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen p-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Content Generator</h1>
            <p class="text-gray-600">Generate jokes and inspirational quotes on demand</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-xl shadow-lg p-8 transition-all duration-300 hover:shadow-xl">
            <!-- Content Type Selection -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">What would you like to generate?</label>
                <select id="contentType" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    <option value="joke">A Funny Joke</option>
                    <option value="quote">An Inspirational Quote</option>
                </select>
            </div>
            
            <!-- Topic Input -->
            <div id="topicContainer" class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Topic (optional)</label>
                <div class="relative">
                    <input type="text" id="topic" 
                           class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                           placeholder="e.g., programming, animals, food...">
                </div>
                <p class="mt-2 text-sm text-gray-500">Leave blank for a random topic</p>
            </div>
            
            <!-- Generate Button -->
            <button id="generate" 
                    class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-6 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition-all duration-200 hover:scale-[1.02]">
                Generate Content
            </button>
            
            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden mt-8">
                <div class="flex items-center justify-center space-x-2">
                    <div class="w-4 h-4 bg-blue-500 rounded-full animate-bounce"></div>
                    <div class="w-4 h-4 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-4 h-4 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
                <p class="text-center text-gray-600 mt-4">Generating your content...</p>
            </div>

            <!-- Content Display -->
            <div id="contentDisplay" class="mt-8 hidden">
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <div id="content" class="text-gray-800 text-lg leading-relaxed"></div>
                    <div id="error" class="text-red-500 mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentType = document.getElementById('contentType');
            const topicContainer = document.getElementById('topicContainer');
            const topic = document.getElementById('topic');
            const generateBtn = document.getElementById('generate');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const contentDisplay = document.getElementById('contentDisplay');
            const contentElement = document.getElementById('content');
            const errorElement = document.getElementById('error');

            // Hide topic input for quotes
            contentType.addEventListener('change', function() {
                if (this.value === 'quote') {
                    topicContainer.classList.add('opacity-0', 'h-0', 'mb-0');
                    topicContainer.classList.remove('mb-8');
                } else {
                    topicContainer.classList.remove('opacity-0', 'h-0', 'mb-0');
                    topicContainer.classList.add('mb-8');
                }
            });

            generateBtn.addEventListener('click', async function() {
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
                        type: contentType.value
                    };
                    
                    if (contentType.value === 'joke' && topic.value.trim()) {
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
                        throw new Error(data.error || 'Failed to generate content');
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
            });
        });
    </script>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out forwards;
        }
    </style>
</body>
</html>