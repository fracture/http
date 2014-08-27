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
        $requestMock = $this->getMock('Fracture\Http\RequestBuilder', ['getAllCookies']);
        $requestMock->expects($this->once())
                    ->method('getAllCookies')
                    ->will($this->returnValue([]));


        $instance = new ResponseBuilder($requestMock);
        $this->assertInstanceOf('Fracture\Http\Response', $instance->create());
    }



    /**
     * @covers Fracture\Http\ResponseBuilder::__construct
     * @covers Fracture\Http\ResponseBuilder::create
     */
    public function testResponseCreatedWithSomeCookies()
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', ['getName'], [], '', false);
        $cookieMock->expects($this->once())
                   ->method('getName')
                   ->will($this->returnValue('name'));



        $requestMock = $this->getMock('Fracture\Http\RequestBuilder', ['getAllCookies']);
        $requestMock->expects($this->once())
                    ->method('getAllCookies')
                    ->will($this->returnValue(['name' => $cookieMock]));


        $instance = new ResponseBuilder($requestMock);
        $this->assertInstanceOf('Fracture\Http\Response', $instance->create());
    }

}
