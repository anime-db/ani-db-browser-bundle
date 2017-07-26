[![Browser for AniDB.net](http://anime-db.org/bundles/animedboffsite/images/anidb.net.png)](http://anidb.net/)

[![Latest Stable Version](https://img.shields.io/packagist/v/anime-db/ani-db-browser-bundle.svg?maxAge=3600&label=stable)](https://packagist.org/packages/anime-db/ani-db-browser-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/anime-db/ani-db-browser-bundle.svg?maxAge=3600)](https://packagist.org/packages/anime-db/ani-db-browser-bundle)
[![Build Status](https://img.shields.io/travis/anime-db/ani-db-browser-bundle.svg?maxAge=3600)](https://travis-ci.org/anime-db/ani-db-browser-bundle)
[![Coverage Status](https://img.shields.io/coveralls/anime-db/ani-db-browser-bundle.svg?maxAge=3600)](https://coveralls.io/github/anime-db/ani-db-browser-bundle?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/anime-db/ani-db-browser-bundle.svg?maxAge=3600)](https://scrutinizer-ci.com/g/anime-db/ani-db-browser-bundle/?branch=master)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/0e383920-eaf5-474a-b998-a00462485827.svg?maxAge=3600&label=SLInsight)](https://insight.sensiolabs.com/projects/0e383920-eaf5-474a-b998-a00462485827)
[![StyleCI](https://styleci.io/repos/19101337/shield?branch=master)](https://styleci.io/repos/19101337)
[![License](https://img.shields.io/packagist/l/anime-db/ani-db-browser-bundle.svg?maxAge=3600)](https://github.com/anime-db/ani-db-browser-bundle)

Browser for AniDB.net
=====================

Read API documentation here: http://wiki.anidb.net/w/HTTP_API_Definition

Installation
------------

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require anime-db/ani-db-browser-bundle
```

Add AnimeDbAniDbBrowserBundle to your application kernel

```php
// app/appKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new AnimeDb\Bundle\AniDbBrowserBundle\AnimeDbAniDbBrowserBundle(),
    );
}
```

Configuration
-------------

```yml
# app/config/config.yml

anime_db_ani_db_browser:
    api:
        # API host
        # As a default used 'http://api.anidb.net:9001'
        host: 'http://api.anidb.net:9001'

        # Prefix for API resurces
        # As a default used '/httpapi/'
        prefix: '/httpapi/'

        # API version
        # As a default used '1'
        protover: 1

    # You must register a client and use it here.
    # See for more info:
    #  - http://anidb.net/perl-bin/animedb.pl?show=client
    #  - https://wiki.anidb.net/w/UDP_Clients
    #  - https://wiki.anidb.net/w/UDP_API_Definition
    app:
        # Verion of your client.
        version: 1

        # Your client name.
        # You point it at registration here: http://anidb.net/perl-bin/animedb.pl?show=client
        client: 'my_home_client'

        # Your client code.
        # You will receive it after registration.
        code: 'api-team-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'
```

Usage
-----

First get a browser

```php
$browser = $this->get('anime_db.ani_db.browser');
```

Get data for anime [Seikai no Monshou](http://anidb.net/perl-bin/animedb.pl?show=anime&aid=1)
([wiki](https://wiki.anidb.info/w/HTTP_API_Definition#Anime)):

```php
$content = $browser->get('anime', ['aid' => 1]);
```

Catch exceptions

```php
use AnimeDb\Bundle\AniDbBrowserBundle\Exception\BannedException;
use AnimeDb\Bundle\AniDbBrowserBundle\Exception\NotFoundException;

try {
    $content = $browser->get('anime', ['aid' => 1]);
} catch (BannedException $e) {
    // you are banned
} catch (NotFoundException $e) {
    // anime not found
} catch (\Exception $e) {
    // other exceptions
}
```

License
-------

This bundle is under the [GPL v3 license](http://opensource.org/licenses/GPL-3.0).
See the complete license in the file: LICENSE
