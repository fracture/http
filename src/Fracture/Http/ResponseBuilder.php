<?php

namespace Fracture\Http;

class ResponseBuilder
{

    private $request;

    private $contentTypes = [];

    public function __construct($request)
    {
        $this->request = $request;
    }


    public function create()
    {
        $instance = new Response;

        $this->applyCookies($instance);
        $this->attemptSettingContentType($instance);

        return $instance;
    }


    private function applyCookies($instance)
    {
        $cookies = $this->request->getAllCookies();

        foreach ($cookies as $cookie) {
            $instance->addCookie($cookie);
        }
    }


    private function attemptSettingContentType($instance)
    {
        $header = $this->request->getAcceptHeader();

        if ($header === null) {
            return;
        }

        foreach ($this->contentTypes as $candidate) {
            if ($header->contains($candidate)) {
                $this->applyContentTypeHeader($instance, $candidate);
                return;
            }
        }
    }


    private function applyContentTypeHeader($instance, $value)
    {
        $header = new Headers\ContentType($value);
        $instance->addHeader($header);
    }

    public function setAvailableContentTypes($contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }
}
