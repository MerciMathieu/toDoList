#Contributing.md

## During developements

### Php cs fixer

Will help you to your php respects standards (psr-12, psr-4)

    php php-cs-fixer.phar fix .

### Test the application with PHPunit

    php bin/phpunit tests/AppBundle

or, with coverage informations,

    php bin/phpunit --coverage-html web/test-coverage


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


### Pull request

Send a pull request via GITHUB to [publication's director] before merging your feature on **master** branch.
Once your developements validated, you will be able to merge on master.


### Travis CI

A job is running each time you push commits on master branch. (see .travis.yml)
It will run all the tests developped in /tests/AppBundle folder and validate the build or not. 

**The build's result has to be validated!**

## Report an issue

You can report an issue by clicking here: [Report an Issue](https://github.com/MerciMathieu/toDoList/issues)