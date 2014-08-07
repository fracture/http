<?php

namespace Fracture\Http;

class ResponseBuilder
{

    private $request;


    public function __construct($request)
    {
        $this->request = $request;
    }


    public function create()
    {
        $cookies = $this->request->getAllCookies();

        $instance = new Response;
        foreach ($cookies as $cookie) {
            $instance->addCookie($cookie);
        }

        return $instance;
    }
}
