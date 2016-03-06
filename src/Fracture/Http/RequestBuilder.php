<?php

namespace Fracture\Http;

class RequestBuilder
{

    private $defaults = [
        'get'    => [],
        'post'   => [],
        'server' => [],
        'files'  => [],
        'cookies'=> [],
    ];

    private $parsers = [];


    /**
     * @param array[] $params
     * @return Routable
     */
    public function create($params)
    {
        $params += $this->defaults;

        $instance = $this->buildInstance();
        $this->applyHeaders($instance, $params['server']);
        $this->applyParams($instance, $params);
        if ($instance->getMethod() !== 'get') {
            $this->applyContentParsers($instance);
        }
        $instance->prepare();

        return $instance;
    }


    /**
     * @param string $type
     * @param callback $parser
     */
    public function addContentParser($type, $parser)
    {
        $this->parsers[$type] = $parser;
    }


    protected function buildInstance()
    {
        $fileBuilder = new UploadedFileBuilder;
        $fileBagBuilder = new FileBagBuilder($fileBuilder);

        return new Request($fileBagBuilder);
    }


    /**
     * @param Request $instance
     */
    protected function applyContentParsers($instance)
    {
        $parameters = [];

        $header = $instance->getContentTypeHeader();

        if ($header === null) {
            return;
        }

        foreach ($this->parsers as $value => $parser) {
            if ($header->contains($value)) {
                $parameters += $this->alterParameters($parser, $value, $header);
            }
        }

        $instance->setParameters($parameters, true);
    }


    private function alterParameters($parser, $value, $header)
    {
        $result = call_user_func($parser, $header);

        if (false === is_array($result)) {
            $message = "Parser for '$value' did not return a 'name => value' array of parameters";
            trigger_error($message, \E_USER_WARNING);
        }

        return $result;
    }


    /**
     * @param Request $instance
     * @param array[] $params
     */
    protected function applyParams($instance, $params)
    {
        $instance->setParameters($params['get']);
        $instance->setParameters($params['post']);
        $instance->setUploadedFiles($params['files']);

        $this->applyWebContext($instance, $params['server']);

        foreach ($params['cookies'] as $name => $value) {
            $instance->addCookie($name, $value);
        }
    }


    /**
     * @param Request $instance
     * @param array[] $params
     */
    protected function applyWebContext($instance, $params)
    {
        if (isset($params['REQUEST_METHOD'])) {
            $instance->setMethod($params['REQUEST_METHOD']);
        }
        if (isset($params['REMOTE_ADDR'])) {
            $instance->setAddress($params['REMOTE_ADDR']);
        }
    }


    /**
     * @param Request $instance
     * @param array $params
     */
    protected function applyHeaders($instance, $params)
    {
        if (array_key_exists('HTTP_ACCEPT', $params)) {
            $header = new Headers\Accept($params['HTTP_ACCEPT']);
            $header->prepare();
            $instance->setAcceptHeader($header);
        }

        if (array_key_exists('CONTENT_TYPE', $params)) {
            $header = new Headers\ContentType($params['CONTENT_TYPE']);
            $header->prepare();
            $instance->setContentTypeHeader($header);
        }
    }
}
