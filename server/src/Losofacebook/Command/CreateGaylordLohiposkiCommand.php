<?php

namespace Losofacebook\Command;

use Everyman\Neo4j\Client;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Keboola\Csv\CsvFile;
use Doctrine\DBAL\Connection;
use Losofacebook\Service\ImageService;
use Losofacebook\Image;
use Everyman\Neo4j\Cypher\Query;

class CreateGaylordLohiposkiCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:create-gaylord-lohiposki')
            ->addArgument('femaleFriends', InputArgument::OPTIONAL, 'number of female friends', 2000)
            ->setDescription('Creates Gaylord Lohiposki');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $femaleFriends = $input->getArgument('femaleFriends');

        $client = $this->getDb();

        $query = new Query(
            $client,
            "MATCH (p:Person) WHERE p.gender = 2 RETURN p LIMIT {limit}",
            [
                'limit' => (int) $femaleFriends
            ]
        );
        $groupies = $query->getResultSet();

        $output->writeln("Will create Gaylord Lohiposki.");

        $image = $this->
            getImageService()
            ->createImage(
                $this->getProjectDirectory() . '/app/dev/gaylord-lohiposki.jpg',
                Image::TYPE_PERSON,
                'male'
            );

        $dbrow = array(
            'gender' => 1,
            'first_name' => 'Gaylord',
            'middle_name' => 'L',
            'last_name' => 'Lohiposki',
            'username' => 'gaylord.lohiposki' ,
            'password' => 'l0h1p05k1',
            'email' => 'gaylord.lohiposki@dr-kobros.com',
            'street_address' => 'Dr. Kobros Vei',
            'zipcode' => null,
            'city' => 'Nordby',
            'state' => 'Akershus',
            'country_code' => 'no',
            'country' => 'Norway',
            'telephone'  => null,
            'mothers_maiden_name' => 'Brattebratten',
            'birthday' => '1960-01-01' ,
            'occupation' => 'Interim CEO',
            'company' => 'Dr. Kobros Foundation',
            'vehicle' => 'Ferrari 458 Spider',
            'url'  => 'http://dr-kobros.com',
            'blood_type'  => 'AB',
            'weight' => 183,
            'height'  => 76,
            'latitude' => '59.936956',
            'longitude' => '10.996628',
            'background_id' => 15,
        );

        $node = $client->makeNode($dbrow);
        $label = $client->makeLabel('Person');

        $node->save();
        $node->addLabels([$label]);

        $node->relateTo($image, 'IS_PICTURED_IN')->save();

        $output->writeln("Will create lots of female friends for Gaylord.");

        foreach ($groupies as $groupie) {
            $node->relateTo($groupie['p'], 'KNOWS')->save();
        }

        $query = new Query(
            $client,
            "MATCH (p:Person)
            OPTIONAL MATCH (p)-[:IS_PICTURED_IN]->(i)
            SET p.primary_image_id = i.uuid
            "
        );
        $query->getResultSet();


    }

    /**
     * @return Client
     */
    public function getDb()
    {
        return $this->getSilexApplication()['neo'];
    }

    /**
     * @return ImageService
     */
    public function getImageService()
    {
        return $this->getSilexApplication()['imageService'];
    }

}
