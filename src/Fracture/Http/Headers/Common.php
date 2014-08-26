<?php

namespace Fracture\Http\Headers;

abstract class Common implements Abstracted
{

    protected $headerName = 'Unspecified';

    protected $headerValue = '';

    protected $list = [];


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
    public function setValue($headerValue = '')
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
        $this->list = [];

        if (strlen($this->headerValue) > 0) {
            $this->list = $this->extractData($this->headerValue);
        }
    }


    abstract protected function extractData($headerValue);


    public function getParsedData()
    {
        return $this->list;
    }
}
