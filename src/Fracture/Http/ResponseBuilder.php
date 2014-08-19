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
        $instance = new Response(new CookieBuilder);

        $cookies = $this->request->getAllCookies();
        $instance->addCookieList($cookies);

        return $instance;
    }
}
