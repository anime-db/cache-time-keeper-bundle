Configuration
=============

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
