Installation
============

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
