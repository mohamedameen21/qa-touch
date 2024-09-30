# QA Touch - Modules & Test Case Management

This project is designed to manage modules and test cases efficiently. Below are the installation steps and requirements for setting up the project.

## Requirements

Ensure your environment meets the following requirements before proceeding:

* **PHP** >= 8.2
* **node** >= 18
* **MySQL** >= 7.0

## Laravel Development Setup

Make sure your environment is set up for Laravel development. You can use Laravel Herd, Laravel Sail, or any other local development tool. You can find more details below:

1. [Laravel Herd](https://herd.laravel.com/)
2. [Laravel Sail](https://laravel.com/docs/sail)

## Installation Instructions

### 1. Clone the Repository

First, pull the repository from GitHub using the following command:

```bash
git clone https://github.com/mohamedameen21/qa-touch
```

### 2. Set Up Your Environment File

Navigate to the project directory and copy the `.env.example` file to create your `.env` file:

```bash
cp .env.example .env
```

### 3. Configure Your Environment

Edit the `.env` file to configure your database settings. Set the `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` variables according to your local database credentials.

### 4. Install PHP Dependencies

Install all PHP dependencies using Composer:

```bash
composer install
```

### 5. Install JavaScript Dependencies

Next, install all the required JavaScript packages using npm:

```bash
npm install
```

### 6. Generate Application Key

Generate a new application key, which will be used to encrypt sensitive data:

```bash
php artisan key:generate
```

### 7. Run Migrations and Seed the Database

To migrate the database and seed it with dummy data, run the following command:

```bash
php artisan migrate:fresh --seed
```

**Note**: This command will create all the necessary tables and populate them with dummy data.

### 8. Dummy User Details

A dummy user is created during the seeding process. You can use the following credentials to log in:

* **Email**: demouser@gmail.com
* **Password**: password

You can also register a new user or sign in using Google.

### 9. Storage Link

To link the storage disk with public folder:

```bash
php artisan storage:link
```

### 9. Serve the Application

To start the development server, use the command below:

```bash
php artisan serve --host=localhost --port=8000
```

The application should now be available at `http://localhost:8000`.

**Note**: You Application should be sereved at this same host name with port number to get Google auth in your local.


## Application Login

You can log into the application in one of the following ways:

1. **Register a new user** and sign in with your credentials.
2. **Sign in using Google**.
3. Use the dummy credentials provided above:
   * **Email**: `demouser@gmail.com`
   * **Password**: `password`

## Demo Video

For a quick walkthrough of the application, you can check out the Loom video I have attached.
