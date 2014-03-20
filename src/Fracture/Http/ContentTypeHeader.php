<?php

namespace Fracture\Http;

class ContentTypeHeader implements AbstractedHeader
{


    private $headerValue = '';

    private $item = [];


    public function __construct($headerValue = '')
    {
        $this->headerValue = $headerValue;
    }


    public function setAlternativeValue($headerValue)
    {
        $this->headerValue = $headerValue;
    }


    public function prepare()
    {
        $this->item = [];

        if (strlen($this->headerValue) > 0) {
            $this->item = $this->extractData($this->headerValue);
        }
    }


    public function extractData($headerValue)
    {
        $result = [];
        $parts = preg_split('#;\s?#', $headerValue, -1, \PREG_SPLIT_NO_EMPTY);

        if (count($parts) === 0) {
            return [];
        }

        $result['value'] = array_shift($parts);

        foreach ($parts as $item) {
            list($key, $value) = explode('=', $item . '=');
            $result[ $key ] = $value;
        }

        return $result;
    }

    public function contains($type)
    {
        return array_key_exists('value', $this->item) && $this->item['value'] === $type;
    }
}
