<?php

namespace Fracture\Http\Headers;

use Fracture\Http\Cookie;

class SetCookie implements Abstracted
{

    protected $headerValue = null;
    protected $headerName = 'Set-Cookie';

    private $cookieName = null;
    private $cookieValue = null;
    private $options = [];

    private $defaults = [
        'expires' => null,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httpOnly' => true,
    ];


    public function __construct($name, $value, $options = [])
    {
        $this->cookieName = $name;
        $this->cookieValue = $value;
        $this->options = $options;
    }


    public function prepare()
    {
        if ($this->hasInvalidOptions($this->options)) {
            $message = 'Valid array keys for cookie options are: \'expires\', \'path\', \'domain\', \'secure\' and \'httpOnly\'';
            trigger_error($message, E_NOTICE);
        }
        $this->options = $this->cleanOptions($this->options);
    }


    private function hasInvalidOptions($options)
    {
        $keys = ['expires', 'path', 'domain', 'secure', 'httpOnly'];
        $wrongKeys = array_diff(array_keys($options), $keys);
        return count($wrongKeys) > 0;
    }


    private function cleanOptions($options)
    {
        $options = $options + $this->defaults;

        if ($options['expires'] !== null) {
            $options['expires'] = $this->convertTime($options['expires']);
        }

        if ($options['path'] === null) {
            $options['path'] = '/';
        }

        $options['domain'] = strtolower($options['domain']);
        $options['secure'] = (bool) $options['secure'];
        $options['httpOnly'] = (bool) $options['httpOnly'];

        return $options;
    }


    private function convertTime($time) {
        if ($this->isDateTime($time)) {
            $time->setTimeZone(new \DateTimeZone('GMT'));
            return $time;
        }

        $time = (int) $time;

        $dateTime = new \DateTime;
        $dateTime->setTimestamp($time);
        $dateTime->setTimeZone(new \DateTimeZone('GMT'));
        return $dateTime;
    }


    private function isDateTime($time)
    {
        return is_object($time) && $time instanceof \DateTime;
    }




    public function getName()
    {
        return $this->headerName;
    }


    public function getValue()
    {
        $name = urlencode($this->cookieName);
        $value = urlencode($this->cookieValue);

        $result = "{$name}={$value}" . $this->collectFormatedOptions();

        return $result;
    }


    private function collectFormatedOptions()
    {
        $options  = $this->collectExpireTime($this->options);
        $options .= $this->collectDomainPathValue($this->options);
        $options .= $this->collectBooleanOptions($this->options);

        return $options;
    }


    private function collectExpireTime($options)
    {
        $string = '';

        if ($options['expires'] !== null) {
            $string = $options['expires']->format(\DateTime::RFC1123);
            $string =  str_replace('+0000', 'GMT', $string);
            $string = '; Expires=' . $string;
        }

        return $string;
    }


    private function collectDomainPathValue($options)
    {
        $output = '';

        if ($options['domain'] !== '') {
            $output .= '; Domain=' . $options['domain'];
        }

        return $output . '; Path=' . $options['path'];
    }


    private function collectBooleanOptions($options)
    {
        $result = '';

        if ($options['secure']) {
            $result .= '; Secure';
        }

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
