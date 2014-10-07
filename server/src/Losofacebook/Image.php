<?php

namespace Losofacebook;

class Image extends Entity
{
    const TYPE_PERSON = 1;
    const TYPE_CORPORATE = 2;

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'uuid' => $this->getUuid(),
            'type' => $this->getType(),
        ];
    }

    public function getUuid()
    {
        return $this->data['uuid'];
    }

    public function getType()
    {
        return $this->data['type'];
    }

}

