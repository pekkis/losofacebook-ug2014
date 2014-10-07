<?php

namespace Losofacebook\Service;

use Doctrine\DBAL\Connection;
use ArrayIterator;
use Everyman\Neo4j\Client;
use Memcached;

abstract class AbstractService
{

    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @var string
     */
    private $nodeLabel;

    /**
     * @var Memcached
     */
    protected $memcached;

    /**
     *
     * @param Client $client
     * @param string $nodeLabel
     * @param Memcached $memcached
     */
    public function __construct(Client $client, $nodeLabel, Memcached $memcached)
    {
        $this->client = $client;
        $this->nodeLabel = $nodeLabel;
        $this->memcached = $memcached;
    }

    public function getNodeLabel()
    {
        return $this->nodeLabel;
    }

}
