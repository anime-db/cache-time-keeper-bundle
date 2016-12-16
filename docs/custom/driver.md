Custom storage driver
=====================

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
> **[BaseDriver](https://github.com/anime-db/cache-time-keeper-bundle/blob/master/src/Service/Driver/DriverInterface.php)**
> as a base class for your custom driver. This class has the implementation of the method of
> **[DriverInterface::getMax()](https://github.com/anime-db/cache-time-keeper-bundle/blob/master/src/Service/Driver/DriverInterface.php#L42)**._

Register custom driver as a service in `service.yml`:

```yml
services:
    cache_time_keeper.driver.custom:
        class: Acme\Bundle\DemoBundle\CacheTimeKeeper\CustomDriver
```

Use custom driver:

```yml
# app/config/config.yml

anime_db_cache_time_keeper:
    use_driver: 'cache_time_keeper.driver.custom'
```
