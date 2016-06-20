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
    # Set as false if you want to disable CacheTimeKeeper and disable HTTP caching.
    enable: true

    # Used driver (multi, memcache, shmop, file or dummy).
    # You can use 'dummy' driver for stores data in a temporary variable, within the current thread of execution
    # program.
    # You can create a custom driver. See below for instructions on creating your own storage driver.
    use_driver: 'file'

    # Set of request headers that trigger "private" Cache-Control behavior on responses that don't explicitly state
    # whether the response is public or private via a Cache-Control directive.
    private_headers: ['Authorization', 'Cookie']

    etag_hasher:
        # You can create a custom ETag hasher driver. See below for instructions on creating your own driver.
        driver: ~

        # Hashing function for build ETag.
        # See http://php.net/manual/en/function.hash.php for more details.
        algorithm: 'sha256'

    track:
        # Disable tracking cache clearing
        clear_cache: true

        # Enable tracking entity individually
        individually_entity: false

    drivers:
        # Multi driver is a wrapper for multiple drivers. Takes the driver with quick access to the data (stored in
        # memory) and slow (stored on hard disc), and receives data on the possibility of fast drivers and if not luck
        # reads data from slow.
        multi:
            # Use 'shmop' driver for store data in memory.
            fast: 'shmop'

            # Use 'file' driver for store data on hard disc.
            slow: 'file'

        # Shmop driver is stores the data in memory using PHP extension Shmop.
        # For work of this driver, you must install 'anime-db/shmop' composer dependence.
        # See http://php.net/manual/en/book.shmop.php and https://github.com/anime-db/shmop for more details.
        shmop:
            # Memory key prefix for use this bundle on shared hosting.
            salt: '%secret%'

        # File driver is store data in a cache dirrectory.
        file:
            # Path for store data.
            path: '%kernel.root_dir%/cache/cache-time-keeper/'

        # Memcache driver is stores the data in memory using PHP extension 'memcache'.
        # See http://php.net/manual/en/book.memcache.php for more details.
        memcache:
            # Cache key prefix
            prefix: 'cache_time_keeper_'

            # Add a memcached server to connection pool
            hosts:
                - {host: 'localhost', port: 11211, weight: 100}
```

## Usage

Adding a new value:

```php
$this->get('cache_time_keeper')->set('foo', new \DateTime());
```

Load saved date:

```php
$date = $this->get('cache_time_keeper')->get('foo');
```

Getting the newest date for a set of keys, taking into account the date of the change project:

```php
$date = $this->get('cache_time_keeper')->getMax('foo');
```

Remove value:

```php
$this->get('cache_time_keeper')->remove('foo');
```

### By entity name

Examples for entity class: `\Acme\Bundle\DemoBundle\Entity\Page`.

Get date last update any of entities:

```php
$date = $this->get('cache_time_keeper')->get('AcmeDemoBundle:Page');
```

Get date last update for entity by id `123`:

```php
$date = $this->get('cache_time_keeper')->get('AcmeDemoBundle:Page:123');
```

Get date last update for entity of composite identifier _(composite primary key)_, for example
`id = 123` and `type = foo`.

```php
$date = $this->get('cache_time_keeper')->get('AcmeDemoBundle:Page:123|foo');
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
        $response->setLastModified($this->get('cache_time_keeper')->getMax([
            'AcmeDemoBundle:Page:'.$id,
            'AcmeDemoBundle:Catalog',
        ]));

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
                'catalogs' => $catalogs,
            ],
            $response,
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
        $response = $this->get('cache_time_keeper')->getResponse([
            'AcmeDemoBundle:Page:'.$id,
            'AcmeDemoBundle:Catalog',
        ]);

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
                'catalogs' => $catalogs,
            ],
            $response,
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
            new JsonResponse(),
        );

        // get entities
        $page = $this->getDoctrine()->getRepository('AcmeDemoBundle:Page')->find($id);
        $catalogs = $this->getDoctrine()->getRepository('AcmeDemoBundle:Catalog')->findAll();

        return $response->setData([
            'page' => $page,
            'catalogs' => $catalogs,
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

## Custom ETag hasher driver

You can create your own hasher driver. You must create service implemented interface
**[DriverInterface](https://github.com/anime-db/cache-time-keeper-bundle/blob/master/src/Service/CacheKeyBuilder/EtagHasherInterface.php)**:

```php
namespace Acme\Bundle\DemoBundle\CacheTimeKeeper;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder\EtagHasherInterface
use FOS\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface

class CustomEtagHasher implements EtagHasherInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $token_storage;

    /**
     * @param TokenStorageInterface $token_storage
     */
    public function __construct(TokenStorageInterface $token_storage)
    {
        $this->token_storage = $token_storage;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return string
     */
    public function hash(Request $request, Response $response)
    {
        $id = 0;

        if (($token = $this->token_storage->getToken()) && ($token->getUser() instanceof User)) {
            $id = $token->getUser()->getId();
        }

        return md5($response->getDate()->format(\DateTime::W3C).'|'.$id);
    }
}
```

Register custom driver as a service in `service.yml`:

```yml
services:
    cache_time_keeper.custom_etag_hasher:
        class: Acme\Bundle\DemoBundle\CacheTimeKeeper\CustomEtagHasher
        arguments: [ '@security.token_storage' ]
```

Use custom driver:

```yml
# app/config/config.yml

anime_db_cache_time_keeper:
    etag_hasher:
        driver: '@cache_time_keeper.custom_etag_hasher'
```

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the file: LICENSE
