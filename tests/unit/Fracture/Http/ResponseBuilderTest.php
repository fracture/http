<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ResponseBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\ResponseBuilder::__construct
     * @covers Fracture\Http\ResponseBuilder::create
     */
    public function testResponseCreatedWithNoCookies()
    {
        $requestMock = $this->getMock('Fracture\Http\Request');

        $instance = new ResponseBuilder($requestMock);
        $this->assertInstanceOf('Fracture\Http\Response', $instance->create());
    }


    /**
     * @covers Fracture\Http\ResponseBuilder::__construct
     * @covers Fracture\Http\ResponseBuilder::create
     * @covers Fracture\Http\ResponseBuilder::setAvailableContentTypes
     *
     * @covers Fracture\Http\ResponseBuilder::attemptSettingContentType
     */
    public function testSimpleExpectedTypesWithMissingAcceptHeader()
    {
        $requestMock = $this->getMock('Fracture\Http\Request', ['getAcceptHeader']);
        $requestMock->expects($this->once())
                    ->method('getAcceptHeader')
                    ->will($this->returnValue(null));;



        $instance = new ResponseBuilder($requestMock);
        $instance->setAvailableContentTypes([
            'text/xml',
            'application/json',
        ]);

        $this->assertInstanceOf('Fracture\Http\Response', $instance->create());

    }


    /**
     * @covers Fracture\Http\ResponseBuilder::__construct
     * @covers Fracture\Http\ResponseBuilder::create
     * @covers Fracture\Http\ResponseBuilder::setAvailableContentTypes
     *
     * @covers Fracture\Http\ResponseBuilder::attemptSettingContentType
     * @covers Fracture\Http\ResponseBuilder::applyContentTypeHeader
     */
    public function testSimpleExpectedTypes()
    {

        $headerMock = $this->getMock('Fracture\Http\Headers\Accept', ['contains']);

        $headerMock->expects($this->exactly(2))
                   ->method('contains')
                   ->will($this->onConsecutiveCalls(false, true));

        $requestMock = $this->getMock('Fracture\Http\Request', ['getAcceptHeader']);
        $requestMock->expects($this->once())
                    ->method('getAcceptHeader')
                    ->will($this->returnValue($headerMock));;



        $instance = new ResponseBuilder($requestMock);
        $instance->setAvailableContentTypes([
            'text/xml',
            'application/json',
        ]);

        $this->assertInstanceOf('Fracture\Http\Response', $instance->create());

    }

}
