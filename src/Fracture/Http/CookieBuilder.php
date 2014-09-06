<?php

namespace Fracture\Http;

class CookieBuilder
{

    public function create($name, $value)
    {
        $instance = new Cookie($name, $value);
        return $instance;
    }
}
