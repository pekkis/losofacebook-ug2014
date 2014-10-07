<?php

namespace Losofacebook\Command;

use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Node;
use Everyman\Neo4j\Relationship;
use Knp\Command\Command;
use Losofacebook\Image;
use Losofacebook\Service\ImageService;
use Rhumsaa\Uuid\Uuid;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Keboola\Csv\CsvFile;
use Doctrine\DBAL\Connection;
use DateTime;




class CreateRandomUsersCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:create-random-losofaces')
            ->addArgument('folder', InputArgument::OPTIONAL, '')
            ->setDescription('Creates loads of random losofaces');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getDb();
        $query = new Query($client, "MATCH (p:Person) OPTIONAL MATCH (p)-[r]->() DELETE p, r");
        $query->getResultSet();

        $output->writeln("Will parse losofaces.");

        list ($males, $females) = $this->getImages();

        $rows = [];

        $finder = $this->getFinder();

        $rows = $this->getRows($finder, $output);

        $nodes = [];

        $count = 0;

        $label = $client->makeLabel('Person');

        $output->writeln('');
        $output->writeln('');


        $nodes = array_map(
            function ($row) use ($client, $output, $label) {

                $node = $client->makeNode($row);
                $node = $node->save();
                $node->addLabels([$label]);

                $output->write('.');

                return $node;

            },
            $rows
        );

        foreach ($nodes as $node) {

            $friends = array_rand($nodes, rand(3, 33));

            $images = ($node->getProperty('gender') == 2) ? $females: $males;

            $imageNo = rand(0, $images->count() - 1);
            $image = $images[$imageNo];

            $node->relateTo($image, 'IS_PICTURED_IN')->save();
            $output->write('.');

            foreach ($friends as $friend) {

                $output->write('.');

                /** @var Node $fn */
                $fn = $nodes[$friend];
                if ($fn->getId() ==  $node->getId()) {
                    continue;
                }

                /** @var Node $node */

                $node->relateTo($fn, 'KNOWS')->save();
            }
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



    private function createImages()
    {
    }

    /**
     * @return ImageService
     */
    public function getImageService()
    {
        return $this->getSilexApplication()['imageService'];
    }


    public function getImages()
    {
        $label = $this->getDb()->makeLabel('Image');

        $males = $label->getNodes('gender', 'male');
        $females = $label->getNodes('gender', 'female');

        return [$males, $females];
    }

    /**
     * @return Finder
     */
    private function getFinder()
    {
        $finder = new Finder();

        $dir = $this->getProjectDirectory() . '/app/dev/fake-names';

        $finder
            ->name('*.bz2')
            ->files()
            ->in($dir);

        foreach ($finder as $file) {

            if (is_readable($file->getRealpath() . '.csv')) {
                continue;
            }

            file_put_contents(
                $file->getRealpath() . '.csv',
                bzdecompress($file->getContents())
            );
        }

        $finder = new Finder();
        $finder
            ->name('*.csv')
            ->files()
            ->in($this->getProjectDirectory() . '/app/dev/fake-names');


        return $finder;


    }

    private function getRows(Finder $finder, OutputInterface $output)
    {
        foreach ($finder as $file) {

            $csv = new CsvFile($file->getRealpath());

            foreach ($csv as $key => $row) {


                if ($key === 0) {
                    if (count($row) < 34) {
                        break;
                    }

                    $headers = array_flip($row);

                    continue;

                }

                $output->write('.');

                $bd = DateTime::createFromFormat('n/j/Y', $row[$headers['Birthday']]) ?: new DateTime('1978-03-21');

                $country = isset($headers['CountryFull']) ? $row[$headers['CountryFull']] : 'us';

                $dbrow = array(
                    'gender' => ($row[$headers['Gender']] == 'male') ? 1 : 2,
                    'first_name' => $row[$headers['GivenName']],
                    'middle_name' => $row[$headers['MiddleInitial']],
                    'last_name' => $row[$headers['Surname']],
                    'username' => $row[$headers['Username']] . uniqid("") ,
                    'password' => $row[$headers['Password']],
                    'email' => $row[$headers['EmailAddress']],
                    'street_address' => $row[$headers['StreetAddress']],
                    'zipcode' => $row[$headers['ZipCode']],
                    'city' => $row[$headers['City']],
                    'state' => $row[$headers['State']],
                    'country_code' => $row[$headers['Country']],
                    'country' => $country,
                    'telephone'  => $row[$headers['TelephoneNumber']],
                    'mothers_maiden_name' => $row[$headers['MothersMaiden']],
                    'birthday' => $bd->format('Y-m-d') ,
                    'occupation' => $row[$headers['Occupation']],
                    'company' => $row[$headers['Company']],
                    'vehicle' => $row[$headers['Vehicle']],
                    'url'  => $row[$headers['Domain']],
                    'blood_type'  => $row[$headers['BloodType']],
                    'weight' => $row[$headers['Kilograms']],
                    'height'  => $row[$headers['Centimeters']],
                    'latitude' => $row[$headers['Latitude']],
                    'longitude' => $row[$headers['Longitude']],
                    'background_id' => rand(1, 30)
                );

                $rows[] = $dbrow;

                if (count($rows) >= 1000) {
                    return $rows;
                }
            }

            $output->write('.');


        }

    }

}
