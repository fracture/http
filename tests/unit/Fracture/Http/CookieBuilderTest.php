<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class CookieBuilderTest extends PHPUnit_Framework_TestCase
{


    /**
     * @covers Fracture\Http\CookieBuilder::create
     */
    public function testCreatingDefaultCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertInstanceOf('Fracture\Http\Cookie' , $cookie);
    }


    /**
     * @covers Fracture\Http\CookieBuilder::create
     */
    public function testValueAndNameOfCreateCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertSame('foo' , $cookie->getName());
        $this->assertSame('bar' , $cookie->getValue());
    }
}
