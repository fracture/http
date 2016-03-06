<?php

namespace Fracture\Http;

class Response
{


    private $headers = [];
    private $cookies = [];

    private $code = 200;
    private $body = '';

    private $hostname = '';

    private $locationHeader = null;



    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }


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


    public function addCookie($name, $value, $options = [])
    {
        $header = new Headers\SetCookie($name, $value, $options);
        $header->prepare();
        $this->cookies[$name] = $header;
    }


    public function removeCookie($name, $options = [])
    {
        $this->addCookie($name, 'deleted', ['expires' => 0] + $options);
    }


    public function addHeader(Headers\Abstracted $header)
    {
        $name = $header->getName();
        $name = strtolower($name);

        if ($name === 'location') {
            $this->locationHeader = $header;
            return;
        }

        $this->headers[$name] = $header;
    }


    public function getHeaders()
    {
        $list = [];

        $list = $this->populateHeaderList($list, $this->cookies);
        $list = $this->populateHeaderList($list, $this->headers);

        if ($this->locationHeader !== null) {
            $this->code = 302;
            $location = $this->locationHeader;

            $value = $this->adjustForHostname($location->getValue());

            $list[] = [
                'value' => $location->getName(). ': ' . $value,
                'replace' => true,
            ];
        }

        return $list;
    }


    private function populateHeaderList($list, $headers)
    {
        foreach ($headers as $header) {

            $name = $header->getName();
            $value = $header->getValue();

            $list[] = [
                'value' => $name . ': ' . $value,
                'replace' => $header->isFinal() === false,
            ];
        }

        return $list;
    }


    private function adjustForHostname($value)
    {
        if (preg_match('#^https?://*#', $value) === 0) {
            $value = $this->hostname . $value;
        }

        return $value;
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
