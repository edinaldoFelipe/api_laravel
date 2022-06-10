# API Rest - Laravel
Simple API Rest Laravel with versioning.

## Requirements
PHP 8.0 or newest
Composer
Mysql Server

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
### Create a new key config
```
php artisan key:generate
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

## Tests
Test by PHPUnit

Run in terminal
```
composer test
```

# API Documentation

# Store New Task
Store a new task and return a object with stored data

> **POST** api/v1/tasks

### Success Response
**Code:** `201 Created`

### Example
```json
{
	"name": "Buy a new flat",
	"file_url": "http:\/\/buy.com.br",
	"updated_at": "2022-06-10T17:18:56.000000Z",
	"created_at": "2022-06-10T17:18:56.000000Z",
	"id": 1
}
```

### Error Response
**Code:** `400 Bad Resquest`
```json
{
	"error": true,
	"message": "O nome da tarefa é obrigatório"
}
```

# Update Expecific Task
Update task and valid status order

> **Put** api/v1/tasks/{id}

### Success Response
**Code:** `204 No Content`

### Error Response
**Code:** `404 Not Found`
```json
{
	"error": true,
	"message": "Registro não encontrado"
}
```
or
```json
{
	"error": true,
	"message": "O status da tarefa não pode ser regredido"
}
```

# Update Task's Status
Update task's status and valid status order

> **PATCH** api/v1/tasks/{id}/status

### Success Response
**Code:** `204 No Content`

### Error Response
**Code:** `404 Not Found`
```json
{
	"error": true,
	"message": "Registro não encontrado"
}
```
or
```json
{
	"error": true,
	"message": "O status da tarefa não pode ser regredido"
}
```

# Store New Task's tag
Check duplicated tag and store a new task's tag

> **POST** api/v1/tasks/{id}/tag

### Success Response
**Code:** `204 No Content`

### Error Response
**Code:** `400 Bad Resquest`
```json
{
	"error": true,
	"message": "O nome da tag é obrigatório"
}
```
or
```json
{
	"error": true,
	"message": "Essa tag já existe para esta tarefa"
}
```

# Get All Tasks
Return array with all tasks

> **GET** api/v1/tasks

### Success Response
**Code:** `200 OK`

### Example
```json
[
	{
		"id": 1,
		"name": "Drink some water",
		"description": "Water is very import to health",
		"status": "WAITING_CUSTOMER_APPROVAL",
		"created_at": "2022-06-10T16:31:34.000000Z",
		"updated_at": "2022-06-10T16:34:30.000000Z",
		"tags": [
			{
				"id": 1,
				"tag_name": "urgent",
				"task_id": 1,
				"created_at": "2022-06-10T16:32:01.000000Z",
				"updated_at": "2022-06-10T16:32:01.000000Z"
			}
		]
	},
	{
		"id": 2,
		"name": "Implement test in project",
		"description": null,
		"status": "BACKLOG",
		"created_at": "2022-06-10T16:39:45.000000Z",
		"updated_at": "2022-06-10T16:39:45.000000Z",
		"tags": []
	}
]
```
or 

**Code:** `204 Not Content`

# Get Task's Link
Return task's link if status is APPROVED

> **GET** api/v1/tasks/{id}/file_url

### Success Response
**Code:** `200 OK`

### Example
```json
{
	"file_url": "https:\/\/mandarin.com.br"
}
```

### Error Response
**Code:** `403 Forbidden`
```json
{
	"error": true,
	"message": "O link só estará disponível após a aprovação da tarefa"
}
```