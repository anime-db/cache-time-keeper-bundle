<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2014, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */
namespace AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Service;

use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Service\CacheKeyBuilder\EtagHasherInterface;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity\Foo;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity\SubNs\Bar;
use AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\TestCase;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Response;

class CacheKeyBuilderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EtagHasherInterface
     */
    protected $etag_hasher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Registry
     */
    protected $doctrine;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManagerInterface
     */
    protected $em;

    /**
     * @var CacheKeyBuilder
     */
    protected $builder;

    protected function setUp()
    {
        $this->etag_hasher = $this->getMock(EtagHasherInterface::class);
        $this->em = $this->getMock(EntityManagerInterface::class);
        $this->doctrine = $this
            ->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->builder = new CacheKeyBuilder($this->etag_hasher);
        $this->builder->setDoctrine($this->doctrine);
    }

    /**
     * @return array
     */
    public function getEntityAlias()
    {
        return [
            [new \stdClass(), null],
            [new Foo(), 'AnimeDbCacheTimeKeeperBundle:Foo'],
            [new Bar(), 'AnimeDbCacheTimeKeeperBundle:SubNs\Bar'],
        ];
    }

    /**
     * @dataProvider getEntityAlias
     *
     * @param object $entity
     * @param string|null $alias
     */
    public function testGetEntityAlias($entity, $alias)
    {
        $conf = $this->getMock(Configuration::class);
        $conf
            ->expects($this->once())
            ->method('getEntityNamespaces')
            ->will($this->returnValue([
                'AcmeDemoBundle' => 'Acme\Bundle\DemoBundle\Entity',
                'AnimeDbCacheTimeKeeperBundle' => 'AnimeDb\Bundle\CacheTimeKeeperBundle\Tests\Entity',
            ]));

        $this->doctrine
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($this->em));

        $this->em
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($conf));

        $this->assertEquals($alias, $this->builder->getEntityAlias($entity));
    }

    public function testGetEntityAliasNoDoctrine()
    {
        $this->builder = new CacheKeyBuilder($this->etag_hasher);

        $this->assertNull($this->builder->getEntityAlias(new Foo()));
    }

    /**
     * @return array
     */
    public function getEntityIdentifier()
    {
        return [
            [[]],
            [['id' => 123]],
            [['id' => 123, 'type' => 'foo']],
        ];
    }

    /**
     * @dataProvider getEntityIdentifier
     *
     * @param array $ids
     */
    public function testGetEntityIdentifier(array $ids)
    {
        $entity = new Bar();

        $meta = $this->getNoConstructorMock(ClassMetadata::class);
        $meta
            ->expects($this->once())
            ->method('getIdentifierValues')
            ->with($entity)
            ->will($this->returnValue($ids));

        $this->doctrine
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($this->em));

        $this->em
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with(get_class($entity))
            ->will($this->returnValue($meta));

        $prefix = null;
        if ($ids) {
            $prefix = CacheKeyBuilder::IDENTIFIER_PREFIX.implode(CacheKeyBuilder::IDENTIFIER_SEPARATOR, $ids);
        }

        $this->assertEquals($prefix, $this->builder->getEntityIdentifier($entity));
    }

    public function testGetEntityIdentifierNoDoctrine()
    {
        $this->builder = new CacheKeyBuilder($this->etag_hasher);

        $this->assertNull($this->builder->getEntityIdentifier(new Bar()));
    }

    public function testGetEtag()
    {
        $etag = 'foo';
        /** @var $response \PHPUnit_Framework_MockObject_MockObject|Response */
        $response = $this->getNoConstructorMock(Response::class);

        $this->etag_hasher
            ->expects($this->once())
            ->method('hash')
            ->with($response)
            ->will($this->returnValue($etag));

        $this->assertEquals($etag, $this->builder->getEtag($response));
    }
}
