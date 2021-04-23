<p style="text-align: center">
    HAAS
</p>

<p style="text-align:center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Project

World-famous astrologer Lolita needs a Horoscope-as-a-Service (HaaS) application,
because she is tired of doing all these complex astrological calculations by hand.

1. Generates horoscopes for all 12 Zodiac signs for a given year. Each sign can have
  from 1 (really shitty day) to 10 (super amazing day).
2. Day scores are generated randomly for each day and stored in the database.
3. Shows a calendar for a given year and Zodiac sign.
4. Days should be colored from #ff0000 (really shitty) to #00ff00 (super amazing).
5. Shows the best month on average (by score) for a Zodiac sign in a given year.
6. Shows which Zodiac sign has the best year (by score)

## How to Install Project
1. run `composer install`
2. run `cp .env .env.example`
3. create Database
4. run `php artisan migrate`
5. run `php artisan serve`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
