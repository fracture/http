<?php

namespace Fracture\Http\Headers;

use Fracture\Http\Cookie;

class SetCookie implements Abstracted
{

    protected $headerValue = null;
    protected $headerName = 'Set-Cookie';


    public function __construct(Cookie $cookie)
    {
        $this->cookie = $cookie;
    }


    public function setValue($headerValue)
    {
        $this->headerValue = $headerValue;
    }


    public function prepare()
    {

    }


    public function getName()
    {
        return $this->headerName();
    }


    public function getValue()
    {
        if ($this->headerValue === null) {
            $this->headerValue = $this->assembleValue($this->cookie);
        }
    }


    private function assembleValue($cookie)
    {
        $name = urlencode($cookie->getName());
        $value = urlencode($cookie->getValue());

        $result = "{$name}={$value}" . $this->collectFormatedOptions($cookie->getOptions());

        return $result;
    }


    private function collectFormatedOptions($options)
    {
        $result = '';

        if ($options['httpOnly']) {
            $result .= '; HttpOnly';
        }

        return $result;
    }


    public function isFinal()
    {
        return true;
    }
}
