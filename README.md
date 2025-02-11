# Symfony Sample Project

This project is a sample backend application developed using Symfony to demonstrate my programming skills in this framework.

## Technologies Used
- Symfony (latest version)
- PHP 8.2+
- PostgreSQL
- Doctrine ORM
- JWT Authentication
- Swagger (NelmioApiDocBundle)
- PHPUnit
- Symfony Security

## Swagger
Swagger UI is available at:
```http
{DOMAIN}/swagger 
```
(http://127.0.0.1:8001/swagger)


## diagrams:
* [ER diagram](https://drive.google.com/file/d/1_edvbmPGw5YYQXE8xZzJMVsL0G-smBdD/view?usp=sharing)


## migration:
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## run

```bash
cd task-management/
symfony serve
```

## tests

Execute this command to run tests:

```bash
cd task-management/
php bin/phpunit
```

if you have not test database created it by these commands:
```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
php bin/console doctrine:schema:create --env=test
```


## Notes
- Entities extend an abstract base entity with common fields.
- Complex database queries are optimized using Doctrine QueryBuilder.
- Business logic is implemented in service classes for maintainability.
- DTOs are used in requests and responses to minimize typos and improve maintainability.



