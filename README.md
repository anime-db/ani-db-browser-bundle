![Browser for AniDB.net](http://anime-db.org/bundles/animedboffsite/images/anidb.net.png)

[![Latest Stable Version](https://poser.pugx.org/anime-db/ani-db-browser-bundle/v/stable.png)](https://packagist.org/packages/anime-db/ani-db-browser-bundle)
[![Latest Unstable Version](https://poser.pugx.org/anime-db/ani-db-browser-bundle/v/unstable.png)](https://packagist.org/packages/anime-db/ani-db-browser-bundle)
[![Total Downloads](https://poser.pugx.org/anime-db/ani-db-browser-bundle/downloads)](https://packagist.org/packages/anime-db/ani-db-browser-bundle)
[![Build Status](https://travis-ci.org/anime-db/ani-db-browser-bundle.svg?branch=master)](https://travis-ci.org/anime-db/ani-db-browser-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/anime-db/ani-db-browser-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/anime-db/ani-db-browser-bundle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anime-db/ani-db-browser-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anime-db/ani-db-browser-bundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0e383920-eaf5-474a-b998-a00462485827/mini.png)](https://insight.sensiolabs.com/projects/0e383920-eaf5-474a-b998-a00462485827)
[![StyleCI](https://styleci.io/repos/19101337/shield)](https://styleci.io/repos/19101337)
[![Dependency Status](https://www.versioneye.com/user/projects/5746f4afce8d0e00360bd712/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/5746f4afce8d0e00360bd712)
[![License](https://poser.pugx.org/anime-db/ani-db-browser-bundle/license.png)](https://packagist.org/packages/anime-db/ani-db-browser-bundle)

Browser for AniDB.net
=====================

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require anime-db/ani-db-browser-bundle
```

Add CacheTimeKeeperBundle to your application kernel

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

### Configuration

Example config:

```yml
# app/config/config.yml

anime_db_ani_db_browser:
    # Used client (guzzle, cache).
    # You can create a custom client. See below for instructions on creating your own client.
    client: 'cache'

    # You must register a client and use it here
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

## Usage

Get data for anime [Seikai no Monshou](http://anidb.net/perl-bin/animedb.pl?show=anime&aid=1)
([wiki](https://wiki.anidb.info/w/HTTP_API_Definition#Anime)):

```php
$this->get('anime_db.ani_db.browser')->getContent('anime', ['aid' => 1]);
```

Get [DomCrawler](http://symfony.com/doc/current/components/dom_crawler.html) for Hot Anime
([wiki](https://wiki.anidb.info/w/HTTP_API_Definition#Hot_Anime)):

```php
$this->get('anime_db.ani_db.browser')->getCrawler('hotanime');
```

## Custom client

You can create your own client. You must create service implemented interface **ClientInterface**:

```php
namespace Acme\Bundle\DemoBundle\AniDbBrowser;

use AnimeDb\Bundle\AniDbBrowserBundle\Service\Client\ClientInterface

class CustomClient implements ClientInterface
{
    public function setTimeout($timeout)
    {
        // set waiting timeout
    }

    public function setProxy($proxy)
    {
        // set HTTP proxy server
    }

    public function get($request, array $params = [])
    {
        // $request is HTTP XML datapage requested (https://wiki.anidb.info/w/HTTP_API_Definition#Parameters)
        // $params is a URI query params

        return ''; // return response
    }
}
```

Register custom client as a service in `service.yml`:

```yml
services:
    anime_db.ani_db.browser.client.custom:
        class: Acme\Bundle\DemoBundle\AniDbBrowser\CustomClient
```

Use custom driver:

```yml
# app/config/config.yml

anime_db_cache_time_keeper:
    use_driver: 'cache_time_keeper.driver.custom'
```

## License

This bundle is under the [GPL v3 license](http://opensource.org/licenses/GPL-3.0).
See the complete license in the file: LICENSE
