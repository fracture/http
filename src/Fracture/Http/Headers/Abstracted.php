<?php

namespace Fracture\Http\Headers;

interface Abstracted
{
    public function setAlternativeValue($headerValue);
    public function extractData($header);
    public function prepare();
}
