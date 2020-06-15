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

### CONFIGURATIONS ###

This module requires some configurations:

* Configuration/Enable: disable or enable module functionality
* Configuration/API Url: ERP URI address
* Configuration/API Key: Token  for API authentication
* Configuration/Reconnection Attempts: amount of reconnection if there is no favorable response from the API.
* Configuration/Cron: Update frequence
* Exhibition/Number of films per line: Quantity movies for line in frontend

For configuration in magento admin painel:

STORES -> CONFIGURATION -> AISLAN -> MOVIES INTEGRATION

### HOW TO TESTE ###

It is necessary to update the database of films in the repository of the api. You can expect to run the cron that is previously configured to run daily or execute a magento cli command:

    bin/magento catalog:movies:update

Please be patiente, this process can be slow

In: 

    ADMIN PAINEL -> CATALOG -> MOVIES
    
will be able to manage available movies

In Add new Movie you can add movie receive by API Integration. Select all movies you want and add by mass save. 
In Catalog Movie you can remove mass or one movie by select option. In the select option you can edit this movie.

A list of movies saved can be viwed in this route: 
   
   {MAGENTO_URI}/catalog_movies/movie/listview

To view the details of the films, just click

### Upcoming updates ###

* Add or remove genres in admin catalog movie painel
* Add SEO and Meta tags for movies in admin painel
* Improve frontend in movie view page
* Add my favorite feature 
* Make movies searchable in the standard magento search bar
