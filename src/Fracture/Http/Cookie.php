<?php

namespace Fracture\Http;

class Cookie
{

    private $name;
    private $value;

    private $options = [
        'expires' => null,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httpOnly' => true,
    ];


    public function __construct($name, $value) {
        $this->name = $name;
        $this->value = $value;
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

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }


    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
