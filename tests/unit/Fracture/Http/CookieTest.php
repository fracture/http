<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class CookieTest extends PHPUnit_Framework_TestCase
{


    /**
     * @covers Fracture\Http\Cookie::__construct
     * @covers Fracture\Http\Cookie::getName
     * @covers Fracture\Http\Cookie::getValue
     * @covers Fracture\Http\Cookie::getOptions
     */
    public function testBasicCookie()
    {
        $instance = new Cookie('name', 'value');

        $this->assertSame('name', $instance->getName());
        $this->assertSame('value', $instance->getValue());

        $this->assertEquals([
            'expires' => null,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => true,
        ], $instance->getOptions());
    }


    /**
     * @covers Fracture\Http\Cookie::__construct
     * @covers Fracture\Http\Cookie::getHeaderValue
     */
    public function testHeaderStringFormation()
    {
        $instance = new Cookie('name', 'value');
        $this->assertSame('name=value; HttpOnly', $instance->getHeaderValue());
    }
}
