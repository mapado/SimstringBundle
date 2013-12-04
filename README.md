SimstringBundle
===============

Mapado SimstringBundle : Symfony bundle for simstring record search

## Installation

This extension use [Simstring](http://www.chokkan.org/software/simstring/). You can find a description of how we use it on mapado.com [on our blog](http://blog.mapado.com/fast-record-search-simstring-php-simstringbundle/).

You have to install [Simsting PHP Extension](http://blog.mapado.com/fast-record-search-simstring-php-simstringbundle/#php-extension-installation) to make this bundle working.

### Get the bundle

Add this in your composer.json

```json
{
	"require": {
		"mapado/simstring-bundle": "1.*"
	}
}
```

and then run

```sh
php composer.phar update
```
or 
```sh
composer update
```
if you installed composer globally.

### Add the classes to your kernel

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Mapado\SimstringBundle\MapadoSimstringBundle(),
    );
}
```

### Configuration
#### Declare your databases

```yaml
# app/config/config.yml
mapado_simstring:
    databases:
        city: "path/to/your/database/city.db"
        country: "path/to/your/database/country.db"
```

#### Declare your readers

```yaml
# app/config/config.yml
mapado_simstring:
    databases:
        city: "path/to/your/database/city.db"
        country: "path/to/your/database/country.db"
    reader:
        city_cosine:
            database: city
            measure: cosine # values are cosine/dice/jaccard/overlap/exact
            threshold: 0.7 # float between 0 and 1
            min_results: 1 # minimim number of results if you have a lower threshold limit
        # you can add as many reader you like
```

#### Declare your writers

```yaml
# app/config/config.yml
mapado_simstring:
    databases:
        city: "path/to/your/database/city.db"
        country: "path/to/your/database/country.db"
    reader:
        city:
            database: city
            measure: cosine # values are cosine/dice/jaccard/overlap/exact (default is: exact)
            threshold: 0.7 # float between 0 and 1
            min_results: 1 # minimim number of results if you have a lower threshold limit
        # you can add as many reader you like

    writer:
        city:
            database: city
            unicode: false # not required
            ngram: 3 # not required
            be: false # not required
```

#### Link with Doctrine ORM
SimstringBundle is compatible with Doctrine ORM.
You only add a few database settings to get objects from your database.

```yaml
# app/config/config.yml
mapado_simstring:
    databases:
        city: 
            path: "path/to/your/database/city.db"
            persistence:
                driver: orm # only ORM is supported for the moment
                model: \Mapado\MyEntityBundle\Entity\City # required
                field: simstringColumn # required
                options: # optional
                    manager: geolocation # if not set, the default manager will be used
                    repository_method: findVisibleBy # findBy is used by default
````




### Usage
You can now use the functions in your controllers or via command line

#### Command Line
```sh
# Perform the query
php app/console mapado:simstring:search city cihcago
# will output chicago

# Perform an insert
php app/console mapado:simstring:insert city chicago houson boston montréal
# will rewrite the database with this cities
```

#### Controller
```php
// Perform the query
$resultList = $this->get('mapado.simstring.city_reader')->find('New Yrok');

// Dynamically change the threshold
$resultList = $this->get('mapado.simstring.city_reader')->find('New Yrok', 0.3);
```

```$resultList``` will be an iterator of ```SimstringResult``` having a string (or an Entity if you used the ORM mapper) as value.
