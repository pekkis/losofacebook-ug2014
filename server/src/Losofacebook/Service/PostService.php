<?php

namespace Losofacebook\Service;
use Doctrine\DBAL\Connection;
use Everyman\Neo4j\Client;
use Losofacebook\Post;
use Losofacebook\Comment;
use Losofacebook\Service\PersonService;
use DateTime;
use Memcached;
use Everyman\Neo4j\Cypher\Query;

/**
 * Image service
 */
class PostService extends AbstractService
{
    public function __construct(Client $client, Memcached $memcached)
    {
        parent::__construct($client, 'Post', $memcached);
    }

    /**
     * Finds by person id
     *
     * @param $path
     */
    public function findByPersonId($personId)
    {
        $query = new Query(
            $this->client,
            "MATCH (pe:Person) WHERE id(pe) = {personId}
            MATCH (po:Post)-[:IS_POSTED_TO]->(pe)
            return po, pe ORDER BY po.date_created
            ",
            [
                'personId' => (int) $personId
            ]
        );


        $res = $query->getResultSet();

        $posts = [];

        foreach ($res as $row) {

            $data = [
                'id' => $row['po']->getId(),
                'date_created' => $row['po']->getProperty('date_created'),
                'poster_first_name' => $row['pe']->getProperty('first_name'),
                'poster_last_name' => $row['pe']->getProperty('last_name'),
                'poster_primary_image_id' => $row['pe']->getProperty('primary_image_id'),
                'content' => $row['po']->getProperty('content'),
            ];

            $posts[] = $data;
        }

        $posts = array_map(
            function ($post) {
                $post = Post::create($post);
                $post->setComments($this->populateComments($post));
                return $post;
            },
            $posts
        );

        return $posts;

    }

    private function populateComments(Post $post)
    {
        $comments = [];

        $query = new Query(
            $this->client,
            "MATCH (po:Post) WHERE id(po) = {postId}
            MATCH (pe:Person)-[:IS_POSTED_BY]-(co:Comment)-[:COMMENTS]-(po)
            return co, pe ORDER BY co.date_created
            ",
            [
                'postId' => (int) $post->getId()
            ]
        );

        $res = $query->getResultSet();

        $comments = [];

        foreach ($res as $row) {

            $data = [
                'id' => $row['co']->getProperty('id'),
                'date_created' => $row['co']->getProperty('date_created'),
                'poster_first_name' => $row['pe']->getProperty('first_name'),
                'poster_last_name' => $row['pe']->getProperty('last_name'),
                'poster_primary_image_id' => $row['pe']->getProperty('primary_image_id'),
                'content' => $row['co']->getProperty('content'),
            ];

            $comments[] = $data;
        }


        $comments = array_map(
            function ($comment) {
                $comment = Comment::create($comment);
                return $comment;
            },
            $comments
        );

        return $comments;
    }
}
