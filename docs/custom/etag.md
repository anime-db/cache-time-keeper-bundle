Custom ETag hasher driver
=========================

You can create your own hasher driver. You must create service implemented interface
**[EtagHasherInterface](https://github.com/anime-db/cache-time-keeper-bundle/blob/master/src/Service/CacheKeyBuilder/EtagHasherInterface.php)**:

```php
namespace Acme\Bundle\DemoBundle\CacheTimeKeeper;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder\EtagHasherInterface;
use FOS\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomEtagHasher implements EtagHasherInterface
{
    /**
     * @var string
     */
    const ETAG_SEPARATOR = '|';

    /**
     * @var RequestStack
     */
    private $request_stack;

    /**
     * @var TokenStorageInterface
     */
    private $token_storage;

    /**
     * @param RequestStack $request_stack
     * @param TokenStorageInterface $token_storage
     */
    public function __construct(RequestStack $request_stack, TokenStorageInterface $token_storage)
    {
        $this->request_stack = $request_stack;
        $this->token_storage = $token_storage;
    }

    /**
     * @param Response $response
     *
     * @return string
     */
    public function hash(Response $response)
    {
        $params = [
            $response->getLastModified()->format(\DateTime::ISO8601),
        ];

        // add cookies to ETag
        if ($this->request_stack->getMasterRequest()) {
            $params[] = http_build_query($this->request_stack->getMasterRequest()->cookies->all());
        }

        // add user id to ETag
        if (($token = $this->token_storage->getToken()) && ($token->getUser() instanceof User)) {
            $params[] = $token->getUser()->getId();
        }

        return hash('sha256', implode(self::ETAG_SEPARATOR, $params));
    }
}
```

Register custom driver as a service in `service.yml`:

```yml
services:
    cache_time_keeper.etag_hasher.custom:
        class: Acme\Bundle\DemoBundle\CacheTimeKeeper\CustomEtagHasher
        arguments: [ '@request_stack', '@security.token_storage' ]
```

Use custom driver:

```yml
# app/config/config.yml

anime_db_cache_time_keeper:
    etag_hasher:
        driver: 'cache_time_keeper.etag_hasher.custom'
```
