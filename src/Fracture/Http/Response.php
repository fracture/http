<?php

namespace Fracture\Http;

class Response
{


    private $headers = [
        'Content-Type' => 'text/html',
    ];

    private $body = '';

    private $code = 200;


    public function setBody($body)
    {
        $this->body = $body;
    }


    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }


    public function setStatusCode($code)
    {
        $this->code = $code !== null ? $code : 200;
    }


    public function send()
    {
        $this->sendHeaders();
        $this->sendBody();
    }


    public function sendHeaders()
    {
        http_response_code($this->code);
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    public function sendBody()
    {
        echo $this->body;
    }
}
