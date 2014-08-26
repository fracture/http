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
        $instance = new Response;
        $cookies = $this->request->getAllCookies();

        foreach ($cookies as $cookie) {
            $instance->addCookie($cookie);
        }

        return $instance;
    }
}
