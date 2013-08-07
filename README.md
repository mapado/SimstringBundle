SimstringBundle
===============

Mapado SimstringBundle : Symfony bundle for simstring record search

## Installation

### Get the bundle

Add this in your composer.json

```json
{
	"require": {
		"mapado/simstring-bundle": "dev-master@dev"
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
        # you can add as many reader you like

    writer:
        city:
            database: city
            unicode: false # not required
            ngram: 3 # not required
            be: false # not required
```




### Usage
You can now use the functions in your controllers

```php
// Perform the query
$textList = $this->get('mapado.simstring.city_reader')->find('New Yrok');

// Dynamically change the threshold
$textList = $this->get('mapado.simstring.city_reader')->find('New Yrok', 0.3);
```
