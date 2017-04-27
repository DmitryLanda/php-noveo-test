php-noveo-test
=========

Simple project created as a for Noveo

Install
-------
### Requirements:
 - php >= 7.0
 - mysql >= 5.7 (may work on lower versions - not checked)
 - Apache2, Nginx or any other web server able to handle php
 
### Steps to install
Please note - steps below adjusted to development mode. 
Steps and configs may be different on production

- `cd /var/www` - or any other dir you like 
- `git clone https://github.com/DmitryLanda/php-noveo-test.git`
- `cd php-noveo-test`
- `composer install` - be sure you have composer installed globally. Add `--no-dev` if you are going to run it production only
- `cp app/config/parameters.yml.dist app/config/parameters.yml` - open it in any editor and modify according your needs
- `php bin/console doctrine:database:create` - it may raise an error if DB already exists
- `php bin/console doctrine:schema:update --force`
- `php bin/console doctrine:fixtures:load --no-interaction` - optional. Fills database with test data
- Configure web server to handle this app 
    - Apache 2.4 example conf:
        ```apacheconfig
          <VirtualHost *:80>
                  ServerName noveo-test
                  DocumentRoot "/var/www/php-noveo-test/web"
                  <Directory "/var/www/php-noveo-test/web">
                          Options FollowSymLinks
                          AllowOverride All
                          Order Deny,Allow
                          Allow from all
                          Require all granted
                  </Directory>
          </VirtualHost>
        ```
    - you ca run it on build in php server instead `php bin/console server:run`
- Add this server into `/etc/hosts` file
    - 127.0.0.1 noveo-test

### Tests
 - `php vendor/bin/simple-phpunit` - To run all tests
 - `php vendor/bin/simple-phpunit --group database` - To run functional tests
 - `php vendor/bin/simple-phpunit --group mock` - To run unit tests
 
API
---
### Users
 - `GET /users` - fetch list of all users
 - `GET /users/:id` - get specified user
 - `POST /users` - create new user
    - `email` required, string
    - `first_name` required, string
    - `last_name` required, string
    - `password` required, string
 - `PATCH /users/:id` - update specified user
    - `email` optional, string
    - `first_name` optional, string
    - `last_name` optional, string
    - `enabled` optional, boolean
    - `password` optional, string
    
### Groups
 - `GET /groups` - fetch list of all groups
 - `GET /groups/:id` - get specified group
 - `POST /groups` - create new group
    - `name` required, string
 - `PATCH /groups/:id` - update existing group
    - `name` required, string
 - `POST /groups/:group_id/users/:user_id` - assign user to group
 - `DELETE /groups/:group_id/users/:user_id` - remove user from group
 
