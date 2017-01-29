# Dis ce que tu veux [![License](https://img.shields.io/badge/license-Apache--2.0-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0.txt)
Application Web - Dis ce que tu veux - Développée dans le cadre du projet PLATINE du Master Informatique E-services à l'Université Lille 1

## Installation
Nécessite l'installation de Symfony 2.8.* pour faire fonctionner le serveur.   
- Installation des dépendances si nécessaire (dossier vendor non présent), nécessite [composer](https://getcomposer.org/download/)  : 'composer update' ou 'php composer.phar update' selon la méthode d'installation  
- Si lors de l'installation des dépendances, il y a une erreur spécifiant que la classe DOMDocument est introuvable, il faut installer php-xml avec : 'sudo apt-get install php7.0-xml' et 'sudo service apache2 restart' peut-être utile juste après  
- Installation des bundles : 'php app/console assets:install' à la racine
- Lancement du serveur : 'php app/console server:run' à la racine  
- Lancement de websocket : 'php app/console gos:websocket:server' à la racine
- Accès au serveur en local : 'http://localhost:8000/' dans un navigateur

### [FOSUserBundle](http://symfony.com/doc/current/bundles/FOSUserBundle/index.html)

Pour le bon fonctionnement de FOSUserBundle, la machine doit avoir mysql, une database "symfony" ainsi qu'un utilisateur.
Pour la configuration de la base de données et des utilisateurs, voir le fichier parameters.yml et [cet article stackoverflow](http://stackoverflow.com/questions/30594962/sqlstatehy000-1045-access-denied-for-user-rootlocalhost-using-password).

Installer Mysql via : sudo apt install php7.0-mysql

Dans Mysql rentrer ces commandes :
- CREATE DATABASE symfony;
- CREATE USER 'dcqtv_admin'@'localhost' IDENTIFIED BY 'teemotroll';
- GRANT ALL PRIVILEGES ON symfony. * TO 'dcqtv_admin'@'localhost';
- FLUSH PRIVILEGES;

Puis rentrer la commande suivante : php app/console doctrine:schema:update --force

Sinon, si php et mysql sont déjà bien installé, utiliser simplement les commandes suivantes :
- composer require friendsofsymfony/user-bundle "~2.0@dev" : Pour installer le bundle dans Symfony
- php app/console doctrine:schema:update --force : Pour initialiser la base de donnée après l'installation du bundle

<!> Ne pas mettre le projet dans un endroit où le path ou chemin pourrait être trop long sur Windows (peut causer une erreur 'Full extraction path exceed MAXPATHLEN (260)')

## Commandes qui peuvent être utiles

- Pour nettoyer le cache : 'php app/console cache:clear --env=prod' à la racine


### Lien vers le blog
[Blog - Dis ce que tu veux](https://discequetuveux.wordpress.com/)

### Liens utiles
[Github - WebSocketBundle](https://github.com/GeniusesOfSymfony/WebSocketBundle)
[Site Symfony - FOSUserBundle](http://symfony.com/doc/current/bundles/FOSUserBundle/index.html)


_2016 - Valentin Ramecourt & Nicolas Vasseur_
