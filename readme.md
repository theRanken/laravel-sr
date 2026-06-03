# 🚀 Laravel Service Repository Generator: Your Code's New Best Friend! 🎉

Hey there, Laravel enthusiast! Are you tired of manually creating services and repositories? Well, say hello to your new coding buddy - the Laravel Service Repository Generator! 

A package to generate service and repository classes for Laravel 10, 11, 12, and 13 applications.
## 🎭 What's This Package All About?

This magical package is here to make your Laravel 10.x life easier, more fun, and way more productive. It's like having a personal code butler at your service!

- PHP 8.1+ for Laravel 10
- PHP 8.2+ for Laravel 11 and 12
- PHP 8.3+ for Laravel 13
- Laravel 10.x, 11.x, 12.x, or 13.x

1. 🧙‍♂️ Generate Services: With a flick of your command wand, create beautiful service classes!
2. 🏗️ Build Repositories: Conjure up repositories faster than you can say "Eloquent"!
3. 🔗 Auto-Binding: Watch in awe as it automatically binds your interfaces to implementations!
4. 🧩 Smart Providers: It even updates your service providers automagically!

Install the package with Composer:

```bash
composer require theranken/laravel-sr-generator
```

Then publish the package assets and register the generated providers:

```bash
php artisan service-repository:install
```

## Compatibility Notes

- Laravel 10 projects register generated providers in `config/app.php`.
- Laravel 11+ projects register generated providers in `bootstrap/providers.php`.
- The package service provider is auto-discovered through Composer.
