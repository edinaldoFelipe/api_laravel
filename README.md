# API Rest - Laravel
Simple API Rest Laravel with versioning.

## Requirements
PHP 8.0 or newest

## Install
### Clone git repository
```
git clone https://github.com/edinaldoFelipe/api_laravel.git api
```

### Open project folder
```
cd api
```

### Install dependencies
```
composer update
```

### Create a database
Create a new database mysql

### Duplicate .env.example
```
cp .env.example .env
```

### Add database credentials
Open the file .env

Edit fields *DB_DATABASE*, *DB_USERNAME* and *DB_PASSWORD* to your database configs.

### Create tables and Populate the database
```
php artisan migrate:fresh --seed
```

### Run local project
```
php artisan serve
```