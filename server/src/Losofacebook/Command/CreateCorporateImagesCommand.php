<?php

namespace Losofacebook\Command;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Keboola\Csv\CsvFile;
use Losofacebook\Service\ImageService;
use Losofacebook\Image;
use Doctrine\DBAL\Connection;


class CreateCorporateImagesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dev:create-corporate-images')
            ->addArgument('skip', InputArgument::OPTIONAL, 0)
            ->setDescription('Creates corporate images');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Will parse corporate images.");

        $db = $this->getDb();



        if (!$input->getArgument('skip')) {

            $db->exec("DELETE FROM image WHERE type = 2");

            $finder = new Finder();

            $finder
                ->files()
                ->in($this->getProjectDirectory() . '/app/dev/imaginarium/corporation');

            $is = $this->getImageService();

            $imageIds = [];

            foreach ($finder as $file) {
                $output->writeln("{$file->getRealpath()}");

                $imageId = $is->createImage($file->getRealpath(), Image::TYPE_CORPORATE);

                if (preg_match('/kobros/', $file->getRealpath())) {
                    $kobrosImageId = $imageId;
                } else {
                    $imageIds[] = $imageId;
                }

            }
        } else {
            $images = $db->fetchAll("SELECT * FROM image WHERE type = 2");

            foreach ($images as $image) {
                if (preg_match('/kobros/', $image['upload_path'])) {
                    $kobrosImageId = $image['id'];
                } else {
                    $imageIds[] = $image['id'];
                }
            }

        }


        $companies = $db->fetchAll("SELECT * FROM company");

        $stmt = $db->prepare("UPDATE company SET primary_image_id = ?, background_id = ? WHERE id = ?");

        foreach ($companies as $company) {

            if ($company['name'] == 'Dr. Kobros Foundation') {
                $imageId = $kobrosImageId;
            } else {
                $imageId = $imageIds[array_rand($imageIds)];
            }

            $stmt->execute([$imageId, rand(1, 30), $company['id']]);
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
