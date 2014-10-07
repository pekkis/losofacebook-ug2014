<?php

namespace Losofacebook;

class Comment extends Entity
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
            'date_created' => $this->getDateCreated(),
            'content' => $this->getContent(),
            'id' => $this->getId(),
        ];
    }

    public function setPoster(Person $poster)
    {
        $this->poster = $poster;
    }

    public function getPoster()
    {
        return $this->poster;
    }

    public function getDateCreated()
    {
        return $this->data['date_created'];
    }

    public function getContent()
    {
        return $this->data['content'];
    }

    public function getId()
    {
        return $this->data['id'];
    }
}

