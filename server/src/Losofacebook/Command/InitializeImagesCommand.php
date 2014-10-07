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

class InitializeImagesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:initialize:images')
            ->setDescription('Initializes images');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $males = [];
        $females = [];

        $paths = [
            'male' => $this->getProjectDirectory() . '/app/dev/imaginarium/people/males',
            'female' => $this->getProjectDirectory() . '/app/dev/imaginarium/people/females'
        ];

        foreach ($paths as $gender => $path) {


            $files = glob("{$path}/*.{jpg,png,gif}", GLOB_BRACE);

            foreach ($files as $file) {
                $node = $this->getImageService()->createImage($file, Image::TYPE_PERSON, $gender);
            }
        }
    }

    /**
     * @return ImageService
     */
    public function getImageService()
    {
        return $this->getSilexApplication()['imageService'];
    }
}
