<a href="https://codeclimate.com/github/MerciMathieu/toDoList/maintainability"><img src="https://api.codeclimate.com/v1/badges/cf196787cd32d09159a9/maintainability" /></a>
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c70d6d0f3dfa4d0b80f578df14394ff8)](https://www.codacy.com/gh/MerciMathieu/toDoList/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=MerciMathieu/toDoList&amp;utm_campaign=Badge_Grade)
[![Build Status](https://travis-ci.com/MerciMathieu/toDoList.svg?branch=master)](https://travis-ci.com/MerciMathieu/toDoList)

# <p>Projet 8 OpenClassrooms - Amérliorez une application existante Todo&Co - PHP / Symfony</p>
## Requirements
*   composer
*   php ^7.2||^8.0
*   Symfony CLI
*   docker
*   docker-compose
*   GIT

## Install
### Clone

    git clone https://github.com/MerciMathieu/toDoList.git

### Run composer

    composer install

### Database
#### Enter your connection's informations
*   Enter your informations in the **/.env**  file  
    Following lines will have to be replaced with your own informations:


    DATABASE_URL=

#### Create the database

    symfony console doctrine:database:create

#### Inject tables with migration

    symfony console doctrine:migrations:migrate

will create the tables and fields

## Usage
### Load initial data

    symfony console doctrine:fixtures:load --group=devGroup

### Change ENV to "production"
replace "dev" by "prod" in .env file

### Start using the website
start symfony web server

    symfony serve -d

start database container

    docker-compose create  

then, start db with

    docker-compose start  

and stop it, when you stop to work on it, with

    docker-compose stop

#### Admin account
Several users has been created when loading fixtures.
ADMIN user credentials:
* login: admin
* password: admin

## Tests
In .env, change "ENV=dev" to "ENV=test"

    php bin/phpunit tests/AppBundle

or, with coverage informations,

    php bin/phpunit --coverage-html web/test-coverage

**Enjoy**
