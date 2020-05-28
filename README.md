# MOVIE CATALOG - M2 #

Magento 2 module: Movie Catalog Challenge

### Instalation ###

* Composer

Add in 'repositories' of composer.json (magento 2 project):

     "repositories": {
        "aislan-movie-catalog": {
            "url": "https://github.com/acedraz/m2-movie-catalog.git",
            "type": "git"
        }
     }

Make a require:

    composer require acedraz/m2-movie-catalog:^1.0

* Manually
    
    Copy files to root/app/code/Aislan/MovieCatalog/
    
### IMPORTANT ###

Run this command in magento cli terminal (if necessary)

    php bin/magento module:enable Aislan_MovieCatalog
    php bin/magento setup:upgrade
