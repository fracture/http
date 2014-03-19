<?php

    namespace Fracture\Http;

    class ContentTypeHeader implements AbstractedHeader{


        private $headerValue = '';

        private $list = [];


        public function __construct( $headerValue = '' )
        {
            $this->headerValue = $headerValue;
        }


        public function setAlternativeValue( $headerValue )
        {
            $this->headerValue = $headerValue;
        }


        public function prepare()
        {
            $this->list = [];

            if ( strlen( $this->headerValue ) > 0 )
            {
                $this->list = $this->getParsedList( $this->headerValue );
            }
        }


        public function getParsedList( $header )
        {
            $elements = preg_split( '#,\s?#', $header, -1, PREG_SPLIT_NO_EMPTY );
            $elements = $this->obtainGroupedElements( $elements );
            return $elements;
        }

        public function contains( $type )
        {
            foreach ( $this->list as $item )
            {
                if ( $item['value'] === $type )
                {
                    return true;
                }
            }

            return false;
        }

        private function obtainGroupedElements( $elements )
        {
            $result = [];

            foreach ( $elements as $item )
            {
                $item = $this->obtainAssessedItem( $item );
                $result[] = $item;
            }

            return $result;
        }


        private function obtainAssessedItem( $item )
        {
            $result = [];
            $parts = preg_split( '#;\s?#', $item, -1, PREG_SPLIT_NO_EMPTY );
            $result['value'] = array_shift( $parts );

            foreach ( $parts as $item )
            {
                list( $key, $value ) = explode( '=', $item . '=' );
                $result[ $key ] = $value;
            }

            return $result;
        }

    }
