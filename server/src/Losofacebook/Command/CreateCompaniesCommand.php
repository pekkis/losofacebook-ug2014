<?php

namespace Losofacebook\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Keboola\Csv\CsvFile;
use Losofacebook\Service\ImageService;
use Doctrine\DBAL\Connection;
use DateTime;

class CreateCompaniesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:create-companies')
            ->setDescription('Creates companies');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Will create companies.");

        $db = $this->getDb();

        $db->exec("DELETE FROM company");

        $companies = $db->fetchAll("SELECT DISTINCT(company) FROM person");
        foreach ($companies as $company) {
            $output->writeln('Inserting ' . $company['company']);
            $db->insert('company', ['name' => $company['company']]);
        }

    }

    /**
     * @return ImageService
     */
    public function getImageService()
    {
        return $this->getSilexApplication()['imageService'];
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this->getSilexApplication()['db'];
    }
}
