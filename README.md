# Dispatch - Content Generation Service

## Overview
Dispatch is a content generation service that uses OpenAI's API to generate jokes and inspirational quotes. It provides both a simple web interface and API endpoints for content generation.

## Features
- On-demand content generation (jokes, quotes)
- Topic-specific joke support
- Clean, modern web interface
- RESTful API

## Prerequisites
- PHP 8.0 or higher
- MySQL/MariaDB
- Composer
- OpenAI API key
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/stevenwett/dispatch.git
cd dispatch
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file and configure your settings:
```bash
cp .env.example .env
```

4. Update the `.env` file with your credentials:
```
# OpenAI Configuration
OPENAI_API_KEY=your-api-key-here

# Database Configuration
DB_HOST=localhost
DB_NAME=dispatch
DB_USER=your_username
DB_PASS=your_password
```

## Usage

### API Endpoint

Generate content using a POST request:
```http
POST /api/content/generate.php
Content-Type: application/json

{
    "type": "joke",
    "topic": "programming"  // optional
}
```

Response:
```json
{
    "success": true,
    "data": {
        "type": "joke",
        "topic": "programming",
        "content": "Why do programmers prefer dark mode? Because light attracts bugs!"
    }
}
```

A web interface is also available at the root URL of your installation.

## Testing
Test the OpenAI integration using:
```bash
php samples/test-content.php
```

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments
- OpenAI for the content generation API