<?php

namespace Losofacebook;

use ArrayIterator;
use DateTime;

class Person extends Entity
{
    /**
     * @var array
     */
    private $friends = [];

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'primaryImageId' => $this->getPrimaryImageId(),
            'backgroundId' => $this->getBackgroundId(),
            'friends' => $this->getFriends(),
            'company' => $this->getCompany(),
            'occupation' => $this->getOccupation(),
            'username' => $this->getUsername(),
            'id' => $this->getId(),
            'birthday' => $this->getBirthday(),
        ];
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->data['first_name'];
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->data['last_name'];
    }

    /**
     * @return string
     */
    public function getPrimaryImageId()
    {
        return $this->data['primary_image_id'];
    }

    /**
     * @return string
     */
    public function getBackgroundId()
    {
        return $this->data['background_id'];
    }

    /**
     * @return array
     */
    public function getFriends()
    {
        return ($this->friends instanceof ArrayIterator) ? $this->friends->getArrayCopy() : $this->friends;
    }

    /**
     * @param array $friends
     */
    public function setFriends(ArrayIterator $friends)
    {
        $this->friends = $friends;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->data['company'];
    }

    /**
     * @return string
     */
    public function getOccupation()
    {
        return $this->data['occupation'];
    }

    public function getUsername()
    {
        return $this->data['username'];
    }

    public function getBirthday()
    {
        $dt = new DateTime($this->data['birthday']);
        return $dt->format(DATE_RFC822);

        // return $this->data['birthday'];
    }

}

