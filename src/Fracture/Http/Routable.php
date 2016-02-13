<?php

namespace Fracture\Http;

interface Routable
{
    public function getUri();
    public function setParameters(array $parameters);
}
