# YouTube Course Scraper

Laravel-based web application that discovers educational YouTube playlists using AI-generated search queries and displays them in a simple responsive UI.

## Features

- Enter multiple categories, one per line
- Generate search queries automatically using AI
- Search YouTube playlists for each generated query
- Store playlists in the database
- Prevent duplicate playlists using YouTube playlist ID
- Filter discovered playlists by category
- Responsive UI built with Bootstrap + custom CSS

## Tech Stack

- Laravel 10
- PHP 8.1+
- MySQL
- Bootstrap 5
- YouTube Data API v3
- Gemini API

## Project Flow

1. User enters categories in a textarea
2. Each category is saved in the database
3. A background job is dispatched for each category
4. AI generates course-style YouTube search queries
5. YouTube Data API searches for playlists
6. Two playlists are stored per generated query
7. Duplicate playlists are ignored based on `youtube_playlist_id`
8. Results are displayed in the UI with category filters and pagination

## Database Structure

### categories

- `id`
- `name`
- `created_at`
- `updated_at`

### playlists

- `id`
- `youtube_playlist_id` (unique)
- `title`
- `description`
- `thumbnail_url`
- `channel_name`
- `created_at`
- `updated_at`

### category_playlist

- `id`
- `category_id`
- `playlist_id`
- `source_query`
- `created_at`
- `updated_at`

## Requirements

Before running the project, make sure you have:

- PHP 8.1 or higher
- Composer
- MySQL
- Laravel compatible extensions enabled
- Internet connection for API requests

## Installation

Clone the repository:

```bash
git clone <your-repo-url>
cd youtube-course-scraper
```

Install dependencies:

composer install

Copy environment file:

cp .env.example .env

Generate application key:

php artisan key:generate
Environment Configuration

Update your .env file with your local database and API keys:

APP_NAME="YouTube Course Scraper"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar
APP_TIMEZONE=Africa/Cairo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youtube_course_scraper
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

YOUTUBE_API_KEY=AIzaSyCeeF61Uwo4pNZO5hDVu2IP7p0JWbnH1BE
GEMINI_API_KEY=AIzaSyAobdNAIB1bJdWZAikvBDzdM3KguXqLiZg
GEMINI_MODEL=gemini-2.5-flash
API Keys

1. YouTube API Key

Used for searching playlists through YouTube Data API v3.

Add it to .env:

YOUTUBE_API_KEY=AIzaSyCeeF61Uwo4pNZO5hDVu2IP7p0JWbnH1BE

2.  Gemini API Key

Used for generating search queries from categories.

Add it to .env:

GEMINI_API_KEY=AIzaSyAobdNAIB1bJdWZAikvBDzdM3KguXqLiZg
GEMINI_MODEL=gemini-2.5-flash

Database Setup

Run migrations:

php artisan migrate
Queue Setup

This project uses Laravel queues to process categories in the background.

Create queue tables if not already migrated:

php artisan queue:table
php artisan queue:failed-table
php artisan migrate

Run the queue worker:

php artisan queue:work
Running the Project

Start the Laravel development server:

php artisan serve

Open:

http://127.0.0.1:8000
How to Use
Open the home page
Enter categories, one per line, for example:
Marketing
Programming
Graphic Design
Business
Engineering
Click Start Fetching
Wait for the queued jobs to finish
Browse discovered playlists in the UI
Filter results by category if needed
Deduplication Logic
Each playlist is uniquely identified by youtube_playlist_id
The playlists table has a unique constraint on youtube_playlist_id
Existing playlists are updated or reused instead of duplicated
Category relations are attached without duplicating pivot rows
Notes
The app is designed to fetch educational playlists
Results depend on the quality of generated AI queries and YouTube availability
Some playlists may belong to more than one category
Queue worker must be running when QUEUE_CONNECTION=database
Optional Development Tip

If you want quick synchronous testing without running a worker, you can temporarily set:

QUEUE_CONNECTION=sync

Then clear config cache:

php artisan config:clear
php artisan cache:clear
Clear Cached Configuration

After changing .env, run:

php artisan config:clear
php artisan cache:clear
php artisan optimize:clear
Screenshots

Add screenshots here before submission if desired:

Home page
Results page
Category filtering
Database tables
Deliverables

This repository includes:

Laravel source code
Database migrations
API integration with Gemini and YouTube
Bootstrap-based frontend with custom CSS
Deduplication logic
README with setup and run instructions

## API Quota Consideration

During development and testing, the number of generated search queries per category was intentionally reduced to avoid exceeding the YouTube Data API daily quota.

The project currently uses configurable environment values:

```env
AI_QUERIES_PER_CATEGORY=2
YOUTUBE_RESULTS_PER_QUERY=2
```
