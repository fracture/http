<?php


    namespace Fracture\Http;

    use Exception;
    use ReflectionClass;
    use PHPUnit_Framework_TestCase;


    class ContentTypeHeaderTest extends PHPUnit_Framework_TestCase
    {


        /**
         * @covers Fracture\Http\ContentTypeHeader::__construct
         * @covers Fracture\Http\ContentTypeHeader::extractData
         */
        public function test_Empty_Instance()
        {
            $instance = new ContentTypeHeader;
            $this->assertEquals( [], $instance->extractData( '' ) );
            $this->assertEquals( [ 'value' => 'application/json' ], $instance->extractData( 'application/json' ) );
            $this->assertEquals( [ 'value' => 'application/json', 'version' => '1' ], $instance->extractData( 'application/json;version=1' ) );
            $this->assertEquals( [ 'value' => 'text/html', 'charset' => 'utf-8' ], $instance->extractData( 'text/html; charset=utf-8' ) );
        }


        /**
         * @covers Fracture\Http\ContentTypeHeader::__construct
         * @covers Fracture\Http\ContentTypeHeader::prepare
         * @covers Fracture\Http\ContentTypeHeader::contains
         */
        public function test_Prepared_Result()
        {
            $instance = new ContentTypeHeader('text/html');
            $instance->prepare();

            $this->assertTrue($instance->contains('text/html'));
            $this->assertFalse($instance->contains('image/png'));
        }


        /**
         * @covers Fracture\Http\ContentTypeHeader::__construct
         * @covers Fracture\Http\ContentTypeHeader::prepare
         * @covers Fracture\Http\ContentTypeHeader::contains
         * @covers Fracture\Http\ContentTypeHeader::setALternativeValue
         */
        public function test_Prepared_Result_Ater_Manual_Alteration()
        {
            $instance = new ContentTypeHeader('application/json');
            $instance->setALternativeValue('image/png');
            $instance->prepare();

            $this->assertTrue($instance->contains('image/png'));
            $this->assertFalse($instance->contains('text/html'));
        }


    }