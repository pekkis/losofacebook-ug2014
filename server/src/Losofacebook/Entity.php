<?php

namespace Losofacebook;

use JsonSerializable;

abstract class Entity implements JsonSerializable
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param array $data
     */
    protected function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * @param array $data
     * @return Person
     */
    public static function create(array $data = [])
    {
        return new static($data);
    }


}
