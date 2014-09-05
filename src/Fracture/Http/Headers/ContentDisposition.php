<?php

namespace Fracture\Http\Headers;

class ContentDisposition extends Common
{

    protected $headerName = 'Content-Disposition';


    protected function extractData($header)
    {
        $matches = null;
        preg_match('/^(.+); *name="(?P<name>[^"]+)"(; *filename="(?P<filename>[^"]+)")?/', $header, $matches);
        return $matches + ['name' => null, 'filename' => null];
    }


    public function getAttribute($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }
}
