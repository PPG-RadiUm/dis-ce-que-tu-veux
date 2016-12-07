# Dis ce que tu veux
Application Web - Dis ce que tu veux - Développée dans le cadre du projet PLATINE du Master Informatique E-services à l'Université Lille 1

## Installation
Nécessite l'installation de Symfony 2.8.* pour faire fonctionner le serveur.   
- Installation des dépendances si nécessaire (dossier vendor non présent), nécessite [composer](https://getcomposer.org/download/)  : 'composer update' ou 'php composer.phar update' selon la méthode d'installation  
- Installation des bundles : 'php app/console assets:install --env=prod' à la racine
- Lancement du serveur : 'php app/console server:run' à la racine  
- Accès au serveur en local : 'http://localhost:8000/' dans un navigateur

<!> Ne pas mettre le projet dans un endroit où le path ou chemin pourrait être trop long sur Windows (peut causer une erreur 'Full extraction path exceed MAXPATHLEN (260)')

## Commandes qui peuvent être utiles

- Pour nettoyer le cache : 'php app/console cache:clear --env=prod' à la racine


### Lien vers le blog
[Blog - Dis ce que tu veux](https://discequetuveux.wordpress.com/)

### Liens utiles
[Github - WebSocketBundle](https://github.com/GeniusesOfSymfony/WebSocketBundle)
_2016 - Valentin Ramecourt & Nicolas Vasseur_
