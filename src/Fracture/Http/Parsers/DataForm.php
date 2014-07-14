<?php

namespace Fracture\Http\Parsers;

class DataForm
{

    private $input;
    private $data = [];

    public function __construct($input)
    {
        $this->input = $input;
    }


    public function prepare()
    {

    }


    public function getParameter($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }
}
