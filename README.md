# Diskusne Forum 
 
 todo - create script for the initial setup of the application

## Initial configuration of the application
- right after you pull thr project repository
- pull the all project php dependencies
 
```composer install```
- pull the project's npm libraries
  
```npm install```

- after pulling all the required dependencies generate the project key 
```php artisan key:generate```
- run the migrations to create tables 
```php artisan migrate```
- run all the test to make sure the initial configuration of the project was done successfully
```phpunit```
- boot up the server without specifying host and port of the application
```php artisan serve```

## Docker

## How to test the application
- run command ```phpunit```
