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
     * @covers Fracture\Http\Cookie::getParameters
     */
    public function testBasicCookie()
    {
        $instance = new Cookie('name', 'value');

        $this->assertSame('name', $instance->getName());
        $this->assertSame('value', $instance->getValue());

        $this->assertEquals([
            'name' => 'name',
            'value' => 'value',
            'expires' => null,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => true,
        ], $instance->getParameters());
    }
}
