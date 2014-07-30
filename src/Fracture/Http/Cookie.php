<?php

namespace Fracture\Http;

class Cookie
{

    private $name;
    private $value;
    private $expires;
    private $path;
    private $domain;
    private $secure;
    private $httpOnly;

    public function __construct(
        $name,
        $value,
        $expires = null,
        $path = null,
        $domain = '',
        $secure = false,
        $httpOnly = true
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->expires = ($expires === null) ? null : (int) $expires;
        $this->path = $path ?: '/';
        $this->domain = strtolower($domain);
        $this->secure = (bool) $secure;
        $this->httpOnly = (bool) $httpOnly;
    }


    public function getValue()
    {
        return $this->value;
    }


    public function getName()
    {
        return $this->name;
    }

    public function getParameters()
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'expires' => $this->expires,
            'path' => $this->path,
            'domain' => $this->domain,
            'secure' => $this->secure,
            'httpOnly' => $this->httpOnly,
        ];
    }
}
