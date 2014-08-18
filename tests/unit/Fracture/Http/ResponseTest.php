<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Response::getBody
     * @covers Fracture\Http\Response::getStatusCode
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testDefaultValues()
    {
        $instance = new Response;
        $this->assertSame(200, $instance->getStatusCode());
        $this->assertSame('', $instance->getBody());
        $this->assertSame([], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::setBody
     * @covers Fracture\Http\Response::appendBody
     * @covers Fracture\Http\Response::prependBody
     * @covers Fracture\Http\Response::getBody
     */
    public function testSimpleBodyOperations()
    {
        $instance = new Response;

        $instance ->setBody('sit');
        $this->assertSame('sit', $instance->getBody());

        $instance->appendBody(' dolor amet');
        $this->assertSame('sit dolor amet', $instance->getBody());

        $instance->prependBody('lorem ipsum ');
        $this->assertSame('lorem ipsum sit dolor amet', $instance->getBody());

        $instance->setBody(null);
        $this->assertNull($instance->getBody());
    }
}
