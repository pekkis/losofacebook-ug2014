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

    /**
     * @param array $params
     */
    public function findByParams(array $params = [], $options = [], callable $callback)
    {

        $qb = $this->conn->createQueryBuilder();
        $qb->select('*')->from($this->tableName, 'tbl');
        foreach ($params as $key => $value) {

            if (is_callable($value)) {
                $value($qb);
            } elseif (is_array($value)) {

                if (!$value) {
                    $value = [-1];
                }

                $qb->andWhere(
                    $qb->expr()->comparison($key, 'IN', '('. implode(', ', $value) . ')')
                );

            } else {
                $qb->andWhere("{$key} = " . $qb->expr()->literal($value));
            }
        }

        if (isset($options['orderBy'])) {

            $orderBy = $options['orderBy'];

            if (!is_array($orderBy)) {
                $orderBy = [$orderBy];
            }

            foreach ($orderBy as $ob) {

                $ob = explode(" ", $ob);

                if (isset($ob[1])) {
                    $qb->orderBy($ob[0], $ob[1]);
                } else {
                    $qb->orderBy($ob[0]);
                }

            }
        }

        if (isset($options['page'])) {
            $qb->setFirstResult(($options['page'] -1) * $options['limit']);
            $qb->setMaxResults($options['limit']);
        }

        $raw = array_map(
            function($data) use ($callback) {
                return $callback($data);
            },
            $this->conn->fetchAll($qb)
        );

        return new ArrayIterator($raw);

    }


    protected function tryCache($cacheId,callable $callback, $lifetime = null)
    {
        if ($ret = $this->memcached->get($cacheId)) {
            return $ret;
        }

        $ret = $callback();

        $this->memcached->set($cacheId, $ret, $lifetime);
        return $ret;
    }

    public function getNodeLabel()
    {
        return $this->nodeLabel;
    }



}
