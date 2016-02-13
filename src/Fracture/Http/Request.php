<?php

namespace Fracture\Http;

class Request implements Routable
{

    private $acceptHeader = null;

    private $contentTypeHeader = null;

    private $method = null;

    private $parameters = [];

    private $files = null;

    private $cookies = [];

    private $fileBagBuilder = null;

    private $address = null;

    private $uri = null;


    public function __construct($fileBagBuilder = null)
    {
        $this->fileBagBuilder = $fileBagBuilder;
    }


    private function getResolvedMethod()
    {
        $method = $this->method;

        // to mimic RESTlike API this lets you define override
        // for request method in form element with name '_method'
        if (array_key_exists('_method', $this->parameters)) {
            $replacement = strtolower($this->parameters['_method']);

            if (in_array($replacement, ['post', 'put', 'delete'])) {
                $method = $replacement;
            }

            unset($this->parameters['_method']);
        }

        return $method;
    }


    private function getResolvedAcceptHeader()
    {
        $header = $this->acceptHeader;

        // lets you override the accept header value,
        // but probably will get removed in a foreseeable future
        if (array_key_exists('_accept', $this->parameters)) {
            $value = strtolower($this->parameters['_accept']);
            $header->setValue($value);
            $header->prepare();
            unset( $this->parameters['_accept']);
        }

        return $header;
    }


    public function prepare()
    {
        $this->method = $this->getResolvedMethod();
        $this->acceptHeader = $this->getResolvedAcceptHeader();
    }


    public function setParameters(array $list, $override = false)
    {
        $duplicates = array_intersect_key($list, $this->parameters);

        // checks of parameters with overlapping names
        if (false === $override && count($duplicates) > 0) {
            $message = implode("', '", array_keys($duplicates));
            $message = "You are trying to override following parameter(s): '$message'";
            trigger_error($message, \E_USER_WARNING);
        }
        $this->parameters = $list + $this->parameters;
    }


    public function getParameter($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        }

        return null;
    }


    public function setMethod($value)
    {
        $method = strtolower($value);
        $this->method = $method;
    }


    public function getMethod()
    {
        return $this->method;
    }


    public function setAcceptHeader(Headers\Accept  $header)
    {
        $this->acceptHeader = $header;
    }


    public function getAcceptHeader()
    {
        return $this->acceptHeader;
    }


    public function setContentTypeHeader(Headers\ContentType $header)
    {
        $this->contentTypeHeader = $header;
    }


    public function getContentTypeHeader()
    {
        return $this->contentTypeHeader;
    }


    public function setUploadedFiles($list)
    {
        if ($this->fileBagBuilder !== null) {
            $list = $this->fileBagBuilder->create($list);
        }

        $this->files = $list;
    }


    public function getUpload($name)
    {
        if (isset($this->files[$name])) {
            return $this->files[$name];
        }

        return null;
    }


    public function addCookie($cookie)
    {
        $name = $cookie->getName();
        $this->cookies[$name] = $cookie;
    }


    public function getCookie($name)
    {
        if (array_key_exists($name, $this->cookies)) {
            return $this->cookies[$name];
        }

        return null;
    }


    public function getAllCookies()
    {
        return $this->cookies;
    }


    protected function resolveUri($uri)
    {
        $parts = explode('/', $uri);
        $segments = [];
        foreach ($parts as $element) {
            $segments = $this->adjustUriSegments($segments, $element);
        }
        return implode('/', $segments);
    }


    /**
     * Method for handling '../' in URL query
     */
    private function adjustUriSegments($list, $item)
    {
        if ($item === '..') {
            array_pop($list);
            return $list;
        }
        array_push($list, $item);

        return $list;
    }


    public function setUri($uri)
    {
        $uri = $this->sanitizeUri($uri);
        $uri = $this->resolveUri($uri);
        $this->uri = '/' . $uri;
    }


    private function sanitizeUri($uri)
    {
        $uri = explode('?', $uri)[0];
        // to remove './' at the start of $uri
        $uri = '/' . $uri;
        $uri = preg_replace(['#(/)+#', '#/(\./)+#'], '/', $uri);
        $uri = trim($uri, '/');
        return $uri;
    }


    public function getUri()
    {
        return $this->uri;
    }


    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        if (filter_var($address, FILTER_VALIDATE_IP) === false) {
            $address = null;
        }

        $this->address = $address;
    }


    public function getAddress()
    {
        return $this->address;
    }
}
