<?php

namespace Fracture\Http\Headers;

interface Abstracted
{
    public function setValue($headerValue);
    public function extractData($header);
    public function prepare();

    public function getFieldName();
    public function getFormatedValue();
}
