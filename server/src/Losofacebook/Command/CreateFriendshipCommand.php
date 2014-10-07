<?php

namespace Losofacebook\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Keboola\Csv\CsvFile;
use Doctrine\DBAL\Connection;
use DateTime;

class CreateFriendshipCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:create-friendship')
            ->setDescription('Creates loads of random losofaces')
            ->addArgument('count', InputArgument::REQUIRED, 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getDb();

        $min = $db->fetchColumn("SELECT MIN(id) FROM person");
        $max = $db->fetchColumn("SELECT MAX(id) FROM person");


        $stmt = $db->prepare("INSERT INTO friendship VALUES(?, ?)");

        for ($x = 1; $x <= $input->getArgument('count'); $x = $x + 1) {

            try {

                $sourceId = rand($min, $max);
                $targetId = rand($min, $max);

                $stmt->execute([$sourceId, $targetId]);
                $output->writeln("Made a friendship between {$sourceId} and {$targetId}");

            } catch (\Exception $e) {
                $output->writeln("Friendship between {$sourceId} and {$targetId} failed");
            }
        }
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this->getSilexApplication()['db'];
    }

}
