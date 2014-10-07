<?php

namespace Losofacebook;

class Post extends Entity
{
    private $poster;

    private $comments = [];

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'poster_last_name' => $this->data['poster_last_name'],
            'poster_first_name' => $this->data['poster_first_name'],
            'poster_primary_image_id' => $this->data['poster_primary_image_id'],
            'comments' => $this->getComments(),
            'date_created' => $this->getDateCreated(),
            'content' => $this->getContent(),
            'id' => $this->getId(),
        ];
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function setComments(array $comments)
    {
        $this->comments = $comments;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getDateCreated()
    {
        return $this->data['date_created'];
    }

    public function getContent()
    {
        return $this->data['content'];
    }
}

