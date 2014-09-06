<?php

namespace Fracture\Http\Headers;

use Fracture\Http\Cookie;

class SetCookie implements Abstracted
{

    protected $headerValue = null;
    protected $headerName = 'Set-Cookie';

    private $default = [
        'expires' => null,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httpOnly' => true,
    ];


    public function __construct(Cookie $cookie, $options = [])
    {
        $this->cookie = $cookie;
        $this->options = $options;
    }


    public function prepare()
    {

    }


    public function setOptions($options)
    {
        if ($this->hasInvalidOptions($options)) {
            $message = 'Valid array keys for cookie options are: \'expires\', \'path\', \'domain\', \'secure\' and \'httpOnly\'';
            trigger_error($message, E_NOTICE);
        }
        $options = $this->cleanOptions($options);
        $this->options = $options;
    }


    private function hasInvalidOptions($options)
    {
        $keys = ['expires', 'path', 'domain', 'secure', 'httpOnly'];
        $wrongKeys = array_diff(array_keys($options), $keys);
        return count($wrongKeys) > 0;
    }


    private function cleanOptions($options)
    {
        $options = $options + $this->options;

        $options['expires'] = (int) $options['expires'];

        if ($options['path'] === null) {
            $options['path'] = '/';
        }

        $options['domain'] = strtolower($options['domain']);
        $options['secure'] = (bool) $options['secure'];
        $options['httpOnly'] = (bool) $options['httpOnly'];

        return $options;
    }


    public function getName()
    {
        return $this->headerName;
    }


    public function getValue()
    {
        $name = urlencode($this->cookie->getName());
        $value = urlencode($this->cookie->getValue());

        $result = "{$name}={$value}" . $this->collectFormatedOptions();

        return $result;
    }


    private function collectFormatedOptions()
    {
        $result = '';

        $options = $this->options;

        // if ($options['httpOnly']) {
        //     $result .= '; HttpOnly';
        // }

        return $result;
    }


    public function isFinal()
    {
        return true;
    }
}
