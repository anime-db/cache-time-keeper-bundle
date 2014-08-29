[![Latest Stable Version](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/v/stable.png)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)
[![Latest Unstable Version](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/v/unstable.png)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)
[![Build Status](https://travis-ci.org/anime-db/cache-time-keeper-bundle.svg?branch=master)](https://travis-ci.org/anime-db/cache-time-keeper-bundle)
[![Total Downloads](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/downloads.png)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)
[![License](https://poser.pugx.org/anime-db/cache-time-keeper-bundle/license.png)](https://packagist.org/packages/anime-db/cache-time-keeper-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/anime-db/cache-time-keeper-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/anime-db/cache-time-keeper-bundle/?branch=master)

# Cache time keeper bundle

The library is intended for quick get a date change entities or entire project, without reference to a database in Symfony2 projects.

Library tracks changes in entities and stores date modified.

_**Warning:** library tracks only changing patterns in general, not each one separately._

## Installation

### Step 1: Download the CacheTimeKeeperBundle

Add the following to the `require` section of your composer.json file:

```
"anime-db/cache-time-keeper-bundle": ">=1.0"
```

And update your dependencies

### Step 2: Enable the bundle

Finally, enable the bundle in the kernel:

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

## Usage

Getting a last modified date of the project:

```php
$this->get('cache_time_keeper')->getMax();
```

or

```php
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\Keeper;

$this->get('cache_time_keeper')->get(Keeper::LAST_UPDATE_KEY);
```

Adding a new value:

```php
$this->get('cache_time_keeper')->set('foo', new \DateTime());
```

Getting the oldest date for a set of keys, taking into account the date of the change project:

```php
$this->get('cache_time_keeper')->getMax('foo');
```

Remove value:

```php
$this->get('cache_time_keeper')->remove('foo');
```

Use in controllers:

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
        $page = $this->getDoctrine()->getManager()->find('AcmeDemoBundle:Page', $id);
        $catalogs = $this->getDoctrine()->getManager()->getRepository('AcmeDemoBundle:Catalog')->findAll();

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

## License

This bundle is under the [MIT license](http://opensource.org/licenses/MIT). See the complete license in the bundle: LICENSE
