<?php

namespace Fracture\Http;

class HeaderFactory
{

    public function create($header)
    {
        $parts = $this->splitEntry($header);

        if (false === $parts) {
            return null;
        }

        list($name, $value) = $parts;
        $name = str_replace('-', '', $name);
        $class = '\Fracture\Http\Headers\\' . $name;

        if (false === class_exists($class)) {
            return null;
        }

        $instance = new $class($value);
        $instance->prepare();
        return $instance;
    }


    public function splitEntry($header)
    {
        $separator = strpos($header, ': ');

        if (false === $separator) {
            return false;
        }

        return [
            substr($header, 0, $separator),
            substr($header, $separator + 2),
        ];
    }
}
