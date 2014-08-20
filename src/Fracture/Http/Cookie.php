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
        if ($this->hasValidOptions($options)) {
            $options = $this->cleanOptions($options);
            $this->options = $options + $this->options;
        }
    }


    private function hasValidOptions($options)
    {
        return true;
    }


    private function cleanOptions($options)
    {
        $results = [];

        if (array_key_exists('expires', $options)) {
            $results['expires'] = (int) $options['expires'];
        }

        if (array_key_exists('path', $options)) {
            $results['path'] = $options['path'] ?: '/';
        }

        if (array_key_exists('domain', $options)) {
            $results['domain'] = strtolower($options['domain']);
        }

        if (array_key_exists('secure', $options)) {
            $results['secure'] = (bool) $options['secure'];
        }

        if (array_key_exists('httpOnly', $options)) {
            $results['httpOnly'] = (bool) $options['httpOnly'];
        }

        return $results;
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


    public function getHeaderValue()
    {
        $value = urlencode($this->value);

        $result = "{$this->name}={$value}";
        $result = $result . $this->collectFormatedOptions();

        return $result;
    }


    private function collectFormatedOptions()
    {
        $result = '';

        if ($this->options['httpOnly']) {
            $result .= '; HttpOnly';
        }

        return $result;
    }
}
