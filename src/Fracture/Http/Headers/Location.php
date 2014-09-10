<?php

namespace Fracture\Http\Headers;

class Location extends Common
{

    protected $headerName = 'Location';


    /**
     * @param string $headerValue
     * @return array[]
     */
    protected function extractData($headerValue)
    {
        return parse_url($headerValue);
    }
}
