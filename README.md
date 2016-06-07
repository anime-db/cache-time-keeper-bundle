[![Latest Stable Version](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/v/stable.png)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)
[![Latest Unstable Version](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/v/unstable.png)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)
[![Total Downloads](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/downloads)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)
[![Build Status](https://travis-ci.org/anime-db/cache-time-keeper-bundle.svg?branch=master)](https://travis-ci.org/anime-db/cache-time-keeper-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/anime-db/cache-time-keeper-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/anime-db/cache-time-keeper-bundle/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/anime-db/cache-time-keeper-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/anime-db/cache-time-keeper-bundle/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4fa810e4-9788-470b-914c-8c09ba0d0be2/mini.png)](https://insight.sensiolabs.com/projects/4fa810e4-9788-470b-914c-8c09ba0d0be2)
[![StyleCI](https://styleci.io/repos/21426266/shield)](https://styleci.io/repos/21426266)
[![Dependency Status](https://www.versioneye.com/user/projects/5746f5f6ce8d0e0047372a2d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5746f5f6ce8d0e0047372a2d)
[![License](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/license.png)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)

# Cache time keeper bundle

The library is intended for quick get a date change entities or entire project, without reference to a database in
Symfony2 projects.

Library tracks changes in entities and stores date modified.

> _**Notice:** library tracks only changing patterns in general, not each one separately._

## Installation

Pretty simple with [Composer](http://packagist.org), run:

```sh
composer require anime-db/cache-time-keeper-bundle
```

Add CacheTimeKeeperBundle to your application kernel

```php
// app/appKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new AnimeDb\Bundle\CacheTimeKeeperBundle\AnimeDbCacheTimeKeeperBundle(),
    );
}
```

### Configuration

Default config:

```yml
# app/config/config.yml

anime_db_cache_time_keeper:
    use_driver: file # Used driver (multi, memcache, shmop, file or dummy)
    track:
        clear_cache: true # Disable tracking cache clearing
    drivers:
        multi:
            fast: shmop # Use 'shmop' driver for store data in memory
            slow: file # Use 'file' driver for store data on hard disc
        shmop:
            salt: '%secret%' # Memory key prefix for use this bundle on shared hosting
        file:
            path: '%kernel.root_dir%/cache/cache-time-keeper/' # Path for store data
        memcache:
            prefix: 'cache_time_keeper_'
            persistent_id: 'cache_time_keeper'
            hosts:
                - {host: 'localhost', port: 11211, weight: 100}
```

## Drivers

In the bundle there are several the data storage drivers.

- **Dummy** (`dummy`) - stores data in a temporary variable, within the current thread of execution program.
- **File** (`file`) - stores data in a file cache *(Default driver)*.
- **Memcache** (`memcache`) - stores the data in memory using PHP extension
    [memcache](http://php.net/manual/en/book.memcache.php).
- **Shmop** (`shmop`) - stores the data in memory using PHP extension [shmop](http://php.net/manual/en/book.shmop.php).
    For work of this driver, you must install [`anime-db/shmop`](https://github.com/anime-db/shmop).
- **Multi** (`multi`) - driver is a wrapper for multiple drivers.
    Takes the driver with quick access to the data (stored in memory) and slow (stored on hard disc), and receives data
    on the possibility of fast drivers and if not luck reads data from slow.
- ***Custom** - see below for instructions on creating your own storage driver.*

## Usage

Adding a new value:

```php
$this->get('cache_time_keeper')->set('foo', new \DateTime());
```

Getting the oldest date for a set of keys, taking into account the date of the change project:

```php
$date = $this->get('cache_time_keeper')->getMax('foo');
```

Remove value:

```php
$this->get('cache_time_keeper')->remove('foo');
```

### Track modify project

Bundle tracks execute the clear cache command (`cache:clear`) and considers this a sign of updated project, and all
cache invalidated. You can reset the date manually by running the command:

```
app/console cache:reset-cache-time-keeper
```

or

```php
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;

$this->get('cache_time_keeper')->set(Keeper::LAST_UPDATE_KEY, new \DateTime());
```

Getting a last modified date of the project:

```php
$date = $this->get('cache_time_keeper')->getMax();
```

or

```php
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;

$date = $this->get('cache_time_keeper')->get(Keeper::LAST_UPDATE_KEY);
```

### Use CacheTimeKeeper in controllers:

```php
namespace Acme\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function indexAction(Request $request, $id)
    {
        // caching
        $response = new Response();
        // cache becomes invalid if changing at least one of the pages, the catalog or the updated project
        $response->setLastModified(
            $this->get('cache_time_keeper')->getMax(['AcmeDemoBundle:Page', 'AcmeDemoBundle:Catalog'])
        );

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // get entities
        $page = $this->getDoctrine()->getRepository('AcmeDemoBundle:Page')->find($id);
        $catalogs = $this->getDoctrine()->getRepository('AcmeDemoBundle:Catalog')->findAll();

        return $this->render(
            'AcmeDemoBundle:Home:index.html.twig',
            [
                'page' => $page,
                'catalogs' => $catalogs
            ],
            $response
        );
    }
}
```

## Usage configured response

You can use a simplified method of configuring a response.

Getting the default  response object:

```php
$response = $this->get('cache_time_keeper')->getResponse();
```

Getting json response limited lifetime:

```php
use Symfony\Component\HttpFoundation\JsonResponse;

$response = $this->get('cache_time_keeper')->getResponse('foo', 3600, new JsonResponse());
```

### Use in controllers:

```php
namespace Acme\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    public function indexAction(Request $request, $id)
    {
        // cache becomes invalid if changing at least one of the pages, the catalog or the updated project
        $response = $this->get('cache_time_keeper')->getResponse(['AcmeDemoBundle:Page', 'AcmeDemoBundle:Catalog']);

        // response was not modified for this request
        if ($response->isNotModified($request)) {
            return $response;
        }

        // get entities
        $page = $this->getDoctrine()->getRepository('AcmeDemoBundle:Page')->find($id);
        $catalogs = $this->getDoctrine()->getRepository('AcmeDemoBundle:Catalog')->findAll();

        return $this->render(
            'AcmeDemoBundle:Home:index.html.twig',
            [
                'page' => $page,
                'catalogs' => $catalogs
            ],
            $response
        );
    }
}
```

## Usage only modified configured response in controllers

```php
namespace Acme\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    public function indexAction(Request $request, $id)
    {
        // cache becomes invalid if changing at least one of the pages, the catalog or the updated project
        $response = $this->get('cache_time_keeper')->getModifiedResponse(
            $request,
            ['AcmeDemoBundle:Page', 'AcmeDemoBundle:Catalog'],
            3600,
            new JsonResponse()
        );

        // get entities
        $page = $this->getDoctrine()->getRepository('AcmeDemoBundle:Page')->find($id);
        $catalogs = $this->getDoctrine()->getRepository('AcmeDemoBundle:Catalog')->findAll();

        return $response->setData([
            'page' => $page,
            'catalogs' => $catalogs
        ]);
    }
}
```

## Custom driver

You can create your own storage driver. You must create service implemented interface
**[DriverInterface](https://github.com/anime-db/cache-time-keeper-bundle/blob/master/src/Service/Driver/DriverInterface.php)**:

```php
namespace Acme\Bundle\DemoBundle\CacheTimeKeeper;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Driver\DriverInterface

class CustomDriver implements DriverInterface
{
    public function get($key)
    {
        // read time from storage for $key

        return null;
    }

    public function set($key, \DateTime $time)
    {
        // save time in storage for $key

        return true;
    }

    public function remove($key)
    {
        // remove time from storage by $key

        return true;
    }

    public function getMax(array $params)
    {
        // find max date for list kays
        return new \DateTime();
    }
}
```

> _You can use
**[BaseDriver](https://github.com/anime-db/cache-time-keeper-bundle/blob/master/src/Service/Driver/DriverInterface.php)**
as a base class for your custom driver. This class has the implementation of the method of
**[DriverInterface::getMax()](https://github.com/anime-db/cache-time-keeper-bundle/blob/master/src/Service/Driver/DriverInterface.php#L42)**._

Register custom driver as a service in `service.yml`:

```yml
services:
    cache_time_keeper.custom:
        class: Acme\Bundle\DemoBundle\CacheTimeKeeper\CustomDriver
```

Use custom driver:

```yml
# app/config/config.yml

anime_db_cache_time_keeper:
    use_driver: '@cache_time_keeper.custom'
```

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
