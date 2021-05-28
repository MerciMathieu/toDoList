#Contribuer efficacement sur le projet Todo&Co

## Stack

* Symfony 5.2
* Php ^7.2 || ^8.0
* Symfony CLI
* docker
* docker-compose

## GIT flow

### Develop new feature

    git checkout master
    git pull origin master
    git checkout -b feature/name-of-feature
    commits on your feature branch
    git push origin feature/name-of-feature

### Work on existant feature

    git checkout feature/name-of-feature
    git pull origin master
    commits on your feature branch
    git push origin feature/name-of-feature

### Merge feature on master

    git checkout master
    git pull origin master
    git merge feature/name-of-feature
    git push origin master

## How to install the application

[README](README.md)

## Test your application with PHPunit

In .env, change "ENV=dev" to "ENV=test"

    php bin/phpunit tests/AppBundle

or, with coverage informations,

    php bin/phpunit --coverage-html web/test-coverage

### Travis CI

A job is running each time you push commits on master branch. (see .travis.yml)

It will run all the tests developped in /tests
folder.

## Good practices
### Php cs fixer

Will make your php respect standards (psr-1, psr-3, psr-4)

    php php-cs-fixer.phar fix .

### Dependency injection

Use dependency injection in methods in your controllers instead of instanciate a new object.

ex: 

    public function exempleMethod(Object $object){}

Dependecy injection documentation: https://symfony.com/doc/current/components/dependency_injection.html

## Controllers

### Make a new controller

    symfony console make:controller

It will create the controller php file, the repository file and the initial template file, in the right folder. 

### Routes

Routes are defined with annotations in controllers.

Routing annotations documentation: https://symfony.com/doc/current/routing.html

## Database
### mysql docker container

Mysql is mounted with Docker. (see /docker-compose.yaml)

To start or build your mysql container

    docker-compose up -d

then, for next times,

    docker-compose start

To stop it (each time you finish to work)

    docker-compose stop


### Doctrine ORM

Documentation doctrine / symfony: https://symfony.com/doc/current/doctrine.html

#### Load fixtures

    symfony console doctrine:fixtures:load --group=devGroup

#### Make and migrate migrations

If you add fields, or change database schema, 

    symfony console make:migration

then

    symfony console doctrine:migrations:migrate

## Security

A part of the application'ssecurity of the application is configured in /config/packages/securiy.yaml

An other part is in **voters** in /src/Security
TaskVoter for exemple, allow us to check user before let him access or delete some tasks. 
(see /src/Security/TaskVoter.php)

Voter documentation: https://symfony.com/doc/current/security/voters.html

## Templating

You can create views in /templates folder.
base.html.twig is the file which is imported in each template file, there is in it
* header
* footer
* content body

Twig documentation: https://twig.symfony.com/doc/2.x/

If you think you have to create a new folder in templates, you should instead create a controller.

## Javascripts and CSS files

You can find these files in /public folder.
To add a css or javascript file, juste create a new file .css or .js in the right folder.

Then, link your js and css in your twig templates.

You can equally find images and fonts in /public folder