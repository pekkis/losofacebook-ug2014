<?php

namespace Losofacebook\Service;
use Doctrine\DBAL\Connection;
use Everyman\Neo4j\Client;
use Everyman\Neo4j\Query\ResultSet;
use Losofacebook\Person;
use DateTime;
use Memcached;

use Everyman\Neo4j\Cypher\Query;

/**
 * Image service
 */
class PersonService extends AbstractService
{

    public function __construct(Client $client, Memcached $memcached)
    {
        parent::__construct($client, 'Person', $memcached);
    }

    /**
     * @param $username
     * @param bool $findFriends
     * @return Person
     */
    public function findByUsername($username, $findFriends = true)
    {
        $query = new Query(
            $this->client,
            "MATCH (p:Person) WHERE p.username = {username}
            OPTIONAL MATCH (p)-[:KNOWS]-(f:Person)
            RETURN p, f ORDER BY f.last_name, f.first_name",
            [
                'username' => $username
            ]
        );

        return $this->createPerson($query->getResultSet());
    }

    /**
     * @param $data
     * @param $fetchFriends
     * @return Person
     */
    protected function createPerson(ResultSet $resultSet)
    {
        $person = Person::create(
            array_merge(
                $resultSet[0]['p']->getProperties(),
                [
                    'id' => $resultSet[0]['p']->getId()
                ]
            )
        );

        $friends = [];

        foreach ($resultSet as $row) {
            $friends[] = Person::create(
                array_merge(
                    $row['f']->getProperties(),
                    [
                        'id' => $row['f']->getId()
                    ]
                )
            );
        }
        $person->setFriends(new \ArrayIterator($friends));

        return $person;
    }
}
