<?php

namespace Fracture\Http\Headers;

abstract class Common implements Abstracted
{

    protected $headerName = 'Unspecified';

    protected $headerValue = '';


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
}
