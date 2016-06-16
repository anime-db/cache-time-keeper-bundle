<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class NotModifiedException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $response
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(Response $response, $code = 0, \Exception $previous = null)
    {
        $this->response = $response;

        parent::__construct(
            Response::$statusTexts[$response->getStatusCode()],
            $code ?: $response->getStatusCode(),
            $previous
        );
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->response->headers->all();
    }
}
