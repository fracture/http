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
        $name = $header->getName();
        $name = strtolower($name);
        $this->headers[$name] = $header;
    }


    public function getHeaders()
    {
        $list = [];

        foreach ($this->headers as $header) {
            $list[] = [
                'value' => $header->getName() . ': ' . $header->getValue(),
                'replace' => $header->isFinal() === false,
            ];
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
