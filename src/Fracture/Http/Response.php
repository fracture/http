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


    public function addCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }


    public function removeCookie($name)
    {
        unset($this->cookies[$name]);
    }


    public function addHeader(Headers\Abstracted $header)
    {
        $name = $header->getName();
        $name = strtolower($name);
        $this->headers[$name] = $header;
    }


    public function getHeaders()
    {
        $list = [];

        foreach ($this->headers as $header) {
            $list[] = $header->getName() . ': ' . $header->getValue();
        }

        foreach ($this->cookies as $cookie) {
            $list[] = 'Set-Cookie: ' . $cookie->getHeaderValue();
        }

        return $list;
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
}
