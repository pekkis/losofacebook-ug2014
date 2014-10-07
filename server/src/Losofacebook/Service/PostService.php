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
     * @param int $personId
     * @param \stdClass $data
     * @return Post
     */
    public function create($personId, $data)
    {
        $cacheId = "post_person_{$personId}";
        $this->memcached->delete($cacheId);

        $data = [
            'person_id' => $personId,
            'poster_id' => $data->poster->id,
            'date_created' => (new DateTime())->format('Y-m-d H:i:s'),
            'content' => $data->content,
        ];

        $this->conn->insert('post', $data);
        $data['id'] = $this->conn->lastInsertId();

        $post = Post::create($data);
        $post->setPoster($this->personService->findById($data['poster_id'], false));
        return $post;
    }

    /**
     * @param int $postId
     * @param \stdClass $data
     * @return Comment
     */
    public function createComment($postId, $data)
    {
        try {


             $post = $this->findByParams(
                 [
                     'id' => $postId
                 ],
                 [],
                 function ($data) {
                    return Post::create($data);
                 }
            )->current();

            if (!$post) {
                throw new \IllegalArgumentException("Invalid post");
            }

            $cacheId = "post_person_{$post->getPersonId()}";
            $this->memcached->delete($cacheId);

            $data = [
                'post_id' => $postId,
                'poster_id' => $data->poster->id,
                'date_created' => (new DateTime())->format('Y-m-d H:i:s'),
                'content' => $data->content,
            ];
            $this->conn->insert('comment', $data);

            $data['id'] = $this->conn->lastInsertId();

            $comment = Comment::create($data);
            $comment->setPoster($this->personService->findById($data['poster_id'], false));
            return $comment;

        } catch (\Exception $e) {
            echo $e;
            die();
        }

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



    public function getComments($postId)
    {
        $data = $this->conn->fetchAll(
            "SELECT * FROM comment WHERE post_id = ? ORDER BY date_created DESC", [$postId]
        );

        $comments = [];
        foreach ($data as $row) {
            $comment = Comment::create($row);
            $comment->setPoster($this->personService->findById($row['poster_id'], false));
            $comments[] = $comment;
        }
        return $comments;
    }

    protected function createPost($data)
    {
        $post = Post::create($data);
        $post->setPoster($this->personService->findById($data['poster_id'], false));
        $post->setComments($this->getComments($data['id']));

        return $post;
    }
}
