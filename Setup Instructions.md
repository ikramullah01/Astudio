## Setup Instructions
Follow the steps below to set up the project on your local machine.

## Prerequisites
Make sure you have the following software installed:

- PHP (8.2 or higher)
- Composer (2.8)
- MySQL
- Laravel (ensure Composer is installed to manage Laravel dependencies)
 

## Step 1: Clone the Repository
 First, clone the repository to your local machine:



```
git clone https://github.com/yourusername/your-repo-name.git
```
## Step 2: Install Dependencies
Navigate to the project directory:



```
cd your-repo-name
```
Install the necessary dependencies using Composer:



```
composer install
```
If you are using frontend tools (like Laravel Mix), run:



```
npm install
```
## Step 3: Set Up Environment Configuration
Duplicate the .env.example file to create a .env file:



```
cp .env.example .env
```
Configure your environment variables in the .env file, such as:

- Database connection:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_HOST=127.0.0.1
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
APP_URL: Set the application URL, e.g., http://localhost for local development.
```

Laravel Passport configuration: You'll need to set up Laravel Passport keys (explained below).

## Step 4: Generate Application Key
Run the following command to generate the application key:



```
php artisan key:generate
```
## Step 5: Set Up Laravel Passport
Laravel Passport is used for API authentication. To set it up, run:



```
php artisan passport:install
```
This command will generate the necessary encryption keys for Passport and create the personal access and password grant clients in the oauth_clients table.

## Step 6: Run Migrations
Run the database migrations to create the necessary tables:



```
php artisan migrate
```
If you want to seed the database with sample data, run:



```
php artisan db:seed
```

This will compile your assets for development.

## Step 7: Testing the Application
Once everything is set up, you can start the development server:


```
php artisan serve
```

By default, the app will be accessible at http://localhost:8000.

## Step 8: Authentication (API)
To authenticate via the API:

1. Register a new user:
- POST request to /api/register with first_name, last_name, email, password.

2. Login to get an access token:
- POST request to /api/login with email, password.
- The response will include an access_token for authenticated requests.

3. Logout:
- POST request to /api/logout with the valid access token in the Authorization header as Bearer {access_token}.