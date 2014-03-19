<?php

    namespace Fracture\Http;

    interface AbstractedHeader
    {

        public function setAlternativeValue( $headerValue );
        public function extractData( $header );
        public function prepare();

    }