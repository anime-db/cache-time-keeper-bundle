Tracking modify project
=======================

Bundle tracks execute the clear cache command (`cache:clear` or `cache:warmup`) and considers this a sign of updated project, and all
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

You can disable tracks execute the clear cache command (`cache:clear` or `cache:warmup`) from config:

```yml
# app/config/config.yml

anime_db_cache_time_keeper:
    track:
        clear_cache: false
```
