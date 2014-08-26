<?php

namespace Fracture\Http\Headers;

interface Abstracted
{
    public function setValue($headerValue);
    public function prepare();

    public function getName();
    public function getValue();
}
