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
use Losofacebook\Service\PersonService;
use Losofacebook\Person;
use DateTime;

class CreatePostCommand extends Command
{

    private $lipsums = [];

    protected function configure()
    {
        $this
            ->setName('dev:create-post')
            ->setDescription('Creates loads of posts for gaylord');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $person = $this->getPersonService()->findByUsername('gaylord.lohiposki');
        $this->createPosts($person, $output, rand(10, 20));

        foreach ($person->getFriends() as $friend) {
            $duh = $this->getPersonService()->findByUsername($friend->getUserName());
            $this->createPosts($duh, $output, rand(10, 20));
        }
    }

    private function createPosts(Person $person, OutputInterface $output, $count)
    {
        $client = $this->getDb();

        $postLabel = $client->makeLabel('Post');
        $commentLabel = $client->makeLabel('Comment');

        $output->write('Creating ' . $count . ' posts for ' . $person->getFirstName() . ' ' . $person->getLastName());

        $postTo = $client->getNode($person->getId());

        for ($posts = 1; $posts <= $count; $posts = $posts + 1) {

            try {

                $potentials = array_merge([$person], $person->getFriends());

                $now = (new DateTime())->format('U');

                if (rand(1, 10) >= 8) {
                    $posterId = $potentials[array_rand($potentials)]->getId();
                } else {
                    $posterId = $person->getId();
                }
                $poster = $client->getNode($posterId);

                $post = [
                    'date_created' => DateTime::createFromFormat('U', rand($now - 500000, $now))->format('U'),
                    'content' => $this->getRandomLipsum(),
                ];

                $postNode = $client->makeNode($post)->save();
                $postNode->addLabels([$postLabel]);

                $postNode->relateTo($postTo, 'IS_POSTED_TO')->save();
                $postNode->relateTo($poster, 'IS_POSTED_BY')->save();

                $commentCount = rand(2, 30);

                $comments = [];

                for ($x = 1; $x <= $commentCount; $x = $x + 1) {

                    if (rand(1, 10) >= 4) {
                        $posterId = $potentials[array_rand($potentials)]->getId();
                    } else {
                        $posterId = $person->getId();
                    }
                    $poster = $client->getNode($posterId);

                    $comment = [
                        'date_created' => DateTime::createFromFormat('U', rand($now - 500000, $now))->format('U'),
                        'content' => $this->getRandomLipsum(),
                    ];

                    $commentNode = $client->makeNode($comment)->save();
                    $commentNode->addLabels([$commentLabel]);

                    $commentNode->relateTo($postNode, 'COMMENTS')->save();
                    $commentNode->relateTo($poster, 'IS_POSTED_BY')->save();

                }

                $output->writeln("Made a post for {$person->getId()}");

            } catch (\Exception $e) {
                $output->writeln("Failed");
            }
        }

    }


    /**
     * @return Client
     */
    public function getDb()
    {
        return $this->getSilexApplication()['neo'];
    }

    /**
     * @return PersonService
     */
    public function getPersonService()
    {
        return $this->getSilexApplication()['personService'];
    }


    protected function getRandomLipsum()
    {
        if (!$this->lipsums) {
            $this->lipsums['short'] = explode("\n\n", file_get_contents("http://loripsum.net/api/2000/short"));
            $this->lipsums['medium'] = explode("\n\n", file_get_contents("http://loripsum.net/api/2000/medium"));

            array_pop($this->lipsums['short']);
            array_pop($this->lipsums['medium']);

        }

        $lengths = ['short', 'medium'];
        $paragraphs = rand(1, 3);

        $lipsum = [];

        for ($x = 1; $x <= $paragraphs; $x = $x + 1) {
            $length = $lengths[array_rand($lengths)];
            $lipsum[] = $this->lipsums[$length][array_rand($this->lipsums[$length])];
        }

        return implode('', $lipsum);
    }

}
