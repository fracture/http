<?php

namespace Fracture\Http;

class CookieBuilder
{

    private $defaults = [
        'expires'   => null,
        'path'      => null,
        'domain'    => '',
        'secure'    => false,
        'httpOnly'  => true,
    ];


    public function __construct(array $defaults = [])
    {
        $this->defaults = $defaults + $this->defaults;
    }


    public function create($name, $value, array $params = [])
    {
        $params = $params + $this->defaults;
        $instance = new Cookie($name, $value,
            $params['expires'], $params['path'], $params['domain'], $params['secure'], $params['httpOnly']);

        return $instance;
    }


    public function setParameter($name, $value)
    {
        if (array_key_exists($name, $this->defaults)) {
            $this->defaults[$name] = $value;
        }
    }
}