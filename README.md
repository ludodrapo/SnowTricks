<p align="center">
  <img src="public/assets/img/snowtricks-logo.png" alt="logo snowtricks" width="300"/>
</p>

# SnowTricks.Com

<a href="https://codeclimate.com/github/codeclimate/codeclimate/maintainability"><img src="https://api.codeclimate.com/v1/badges/a99a88d28ad37a79dbf6/maintainability" /></a>
<a href="https://codeclimate.com/github/codeclimate/codeclimate/test_coverage"><img src="https://api.codeclimate.com/v1/badges/a99a88d28ad37a79dbf6/test_coverage" /></a>

Project number 6 from the OpenClassRooms cursus on PHP/Symfony developpement.

Coded by Ludo Drapo with Symfony 5.3, php 7.4.12 and MySql 5.7.

Visually based on the Vesperr Template created by BootstapMade.Com

To "try it at home", you can download these files, or clone this repository.

You'll have to configure your .env.local with the access to your database server like this
```
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql:/db_/user:db_password@127.0.0.1:8889/db_name?serverVersion=5.7"
###> doctrine/doctrine-bundle ###
```
then run
```
% composer install
```
And after that
```
% composer prepare
```
Finaly, you will have to configure your DSN-MAILER (for instance mailtrap.io) in your env.local too
```
###> symfony/mailer ###
MAILER_DSN= (...)
###> symfony/mailer ###
```
