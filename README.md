# URL Shortener

## Local Setup Instructions

1. Clone the repository:

```bash
git clone YOUR_GITHUB_REPOSITORY_URL
```

2. Go to the project directory:

```bash
cd assignment
```

3. Install dependencies:

```bash
composer install
```

4. Create the environment file:

```bash
cp .env.example .env
```

5. Generate the application key:

```bash
php artisan key:generate
```

6. Configure your database credentials in the `.env` file.

7. Run migrations and seed the database:

```bash
php artisan migrate --seed
```

8. Start the Laravel development server:

```bash
php artisan serve
```

9. Open the application:

## AI Usage Disclosure

- **AI Tool:** ChatGPT
- **Purpose:** Used for understanding the assignment document, resolving errors during development, and getting help with HTML/CSS.

```text


http://127.0.0.1:8000
```
