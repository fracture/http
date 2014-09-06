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
     */
    public function testBasicCookie()
    {
        $instance = new Cookie('name', 'value');

        $this->assertSame('name', $instance->getName());
        $this->assertSame('value', $instance->getValue());
    }


    /**
     * @covers Fracture\Http\Cookie::__construct
     * @covers Fracture\Http\Cookie::setValue
     * @covers Fracture\Http\Cookie::getValue
     */
    public function testChangingValue()
    {
        $instance = new Cookie('name', 'value');
        $instance->setValue('new value');
        $this->assertSame('new value', $instance->getValue());
    }
}
