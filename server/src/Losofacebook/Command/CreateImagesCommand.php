<?php

namespace Losofacebook\Command;

use Everyman\Neo4j\Client;
use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Keboola\Csv\CsvFile;
use Losofacebook\Service\ImageService;
use Losofacebook\Image;
use Doctrine\DBAL\Connection;

use DateTime;

class CreateImagesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:recreate:images')
            ->setDescription('Recreates images for users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Will recreate images.");

        $db = $this->getDb();
        $imageService = $this->getImageService();

        $images = $db->makeLabel('Image')->getNodes();

        foreach ($images as $image) {
            $imageService->createVersions($image->getProperty('uuid'));
            $output->writeln("Recreating versions for #{$image->getProperty('uuid')}");
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
     * @return Client
     */
    public function getDb()
    {
        return $this->getSilexApplication()['neo'];
    }
}
