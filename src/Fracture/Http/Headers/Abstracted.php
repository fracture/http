<?php

namespace Fracture\Http\Headers;

interface Abstracted
{
    public function prepare();

    public function getName();
    public function getValue();


    public function isFinal();
}
