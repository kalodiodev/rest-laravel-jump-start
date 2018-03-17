[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://travis-ci.org/kalodiodev/rest-laravel-jump-start.svg?branch=master)](https://travis-ci.org/kalodiodev/rest-laravel-jump-start)
[![codecov](https://codecov.io/gh/kalodiodev/rest-laravel-jump-start/branch/master/graph/badge.svg)](https://codecov.io/gh/kalodiodev/rest-laravel-jump-start)

# Restful Laravel Jump Start
Laravel web application skeleton for starting faster REST projects development.

## Installation

### Step 1.
Clone this repository and install all Composer dependencies.
```
git clone git@github.com:kalodiodev/rest-laravel-jump-start.git
cd rest-laravel-jump-start
composer install
```

### Step 2.
Rename or Copy .env.example file to .env and generate application key.
```
cp .env.example .env
php artisan key:generate
```

### Step 3.
Create a new database and reference its name and username/password within the project's `.env` file. Like the example below
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restlaravel
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4.
Migrate database
```
php artisan migrate
```

### Step 5.
Create the encryption keys needed to generate secure access tokens and also create "personal access" and "password grant" clients
```
php artisan passport:install
```

## Frameworks/Libraries
* [laravel/laravel](https://github.com/laravel/laravel) - A PHP Framework For Web Artisans
* [laravel/passport](https://github.com/laravel/passport)