<?php

namespace Fracture\Http\Headers;

abstract class Common implements Abstracted
{

    protected $headerName = 'Unspecified';

    protected $headerValue = '';

    protected $data = null;


    /**
     * @param string $headerValue
     */
    public function __construct($headerValue = '')
    {
        $this->headerValue = $headerValue;
    }


    /**
     * @param string $headerValue
     */
    public function setValue($headerValue)
    {
        $this->headerValue = $headerValue;
    }


    public function getName()
    {
        return $this->headerName;
    }

    public function getValue()
    {
        return $this->headerValue;
    }


    public function prepare()
    {
        $this->data = null;

        if (strlen($this->headerValue) > 0) {
            $this->data = $this->extractData($this->headerValue);
        }
    }


    abstract protected function extractData($headerValue);


    public function getParsedData()
    {
        return $this->data;
    }


    public function isFinal()
    {
        return false;
    }


    public function getParameter($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }
}
