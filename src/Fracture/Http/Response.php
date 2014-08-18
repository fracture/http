<?php

namespace Fracture\Http;

class Response
{


    private $headers = [];
    private $cookies = [];


    private $code = 200;
    private $body = '';


    public function setBody($content)
    {
        $this->body = $content;
    }


    public function appendBody($content)
    {
        $this->body = $this->body . $content;
    }


    public function prependBody($content)
    {
        $this->body = $content . $this->body;
    }


    public function getBody()
    {
        return  $this->body;
    }


    public function addHeader(Headers\Abstracted $header)
    {
        $this->headers[$headers->getFieldName()] = $header;
    }


    public function addCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }


    public function getHeaders()
    {
        return $this->headers;
    }


    public function setStatusCode($code)
    {
        $code = (int)$code;

        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Invalid response status code');
        }

        $this->code = $code;
    }


    public function getStatusCode()
    {
        return $this->code;
    }



    public function sendHeaders()
    {
        http_response_code($this->code);
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

}
