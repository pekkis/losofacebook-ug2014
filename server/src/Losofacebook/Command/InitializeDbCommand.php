<?php

namespace Losofacebook\Command;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeDbCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:initialize:db')
            ->setDescription('Initializes db');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Cleaning DB");

        $client = $this->getDb();
        $query = new Query($client, "MATCH (n) OPTIONAL MATCH (n)-[r]->() DELETE n, r");
        $query->getResultSet();

        $output->writeln("Cleaned DB");
    }

    /**
     * @return Client
     */
    public function getDb()
    {
        return $this->getSilexApplication()['neo'];
    }
}
