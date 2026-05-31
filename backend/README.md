# TaskForge Backend

Laravel 12 API for TaskForge.

## Stack

- Laravel 12
- Laravel Sanctum
- Laravel Socialite
- SQLite
- PHPUnit

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve --host=127.0.0.1 --port=8000
```

## Tests

```bash
vendor/bin/pint --dirty
php artisan test
```

## Deployment

The backend is prepared for Railway. See the root [DEPLOYMENT.md](../DEPLOYMENT.md).
