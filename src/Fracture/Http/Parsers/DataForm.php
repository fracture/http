<?php

namespace Fracture\Http\Parsers;

class DataForm
{

    private $input;
    private $boundry;

    private $data = [];

    public function __construct($input, $boundry = null)
    {
        $this->input = $input;
        $this->boundry = $boundry;
    }


    public function prepare()
    {
        $file = new \SPLFileObject($this->input);
        $first = true;
        foreach ($file as $line) {
            if ($first && strpos($line, $boundy)) {
                $separator = $line;
                $first = false;
            }

            if ($line === $separator) {

            }
//            var_Dump($line);
        }
    }


    public function getParameter($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }
}
