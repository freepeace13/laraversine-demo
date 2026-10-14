# Laraversine demo

A one-page demo of [freepeace13/laraversine](https://github.com/freepeace13/laraversine):
pick where you are, a radius and a unit, and 24 seeded places re-sort by distance.

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

- Query: `routes/web.php`
- Model: `app/Models/Place.php` (extends `Laraversine\Destination`)
- SQLite function registration: `app/Providers/AppServiceProvider.php`

Requires `freepeace13/laraversine` ^1.1 from Packagist.
