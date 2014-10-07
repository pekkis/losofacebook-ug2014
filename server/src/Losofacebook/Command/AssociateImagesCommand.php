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

class AssociateImagesCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('dev:associate-images')
            ->setDescription('Associates images for all users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getDb();

        $persons = $db->fetchAll("SELECT id, gender FROM person WHERE username <> 'gaylord.lohiposki'");

        $femalesMin = $db->fetchColumn("SELECT MIN(id) FROM image WHERE upload_path LIKE '%/females/%'");
        $femalesMax = $db->fetchColumn("SELECT MAX(id) FROM image WHERE upload_path LIKE '%/females/%'");

        $malesMin = $db->fetchColumn("SELECT MIN(id) FROM image WHERE upload_path LIKE '%/males/%'");
        $malesMax = $db->fetchColumn("SELECT MAX(id) FROM image WHERE upload_path LIKE '%/males/%'");

        $sql = "UPDATE person SET primary_image_id = ?, background_id = ? WHERE id = ?";
        $stmt = $db->prepare($sql);

        foreach ($persons as $person) {

            $output->writeln("Associating #{$person['id']}");

            if ($person['gender'] == 1) {
                $primaryImageId = rand($malesMin, $malesMax);
            } else {
                $primaryImageId = rand($femalesMin, $femalesMax);
            }

            $backgroundId = rand(1, 30);
            $stmt->execute(array($primaryImageId, $backgroundId, $person['id']));

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
