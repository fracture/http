<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class CookieBuilderTest extends PHPUnit_Framework_TestCase
{


    /**
     * @covers Fracture\Http\CookieBuilder::__construct
     * @covers Fracture\Http\CookieBuilder::create
     */
    public function testCreatingDefaultCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertInstanceOf('Fracture\Http\Cookie' , $cookie);
    }


    /**
     * @covers Fracture\Http\CookieBuilder::__construct
     * @covers Fracture\Http\CookieBuilder::create
     */
    public function testValueAndNameOfCreateCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertSame('foo' , $cookie->getName());
        $this->assertSame('bar' , $cookie->getValue());
    }


    /**
     * @covers Fracture\Http\CookieBuilder::__construct
     * @covers Fracture\Http\CookieBuilder::create
     */
    public function testCompleteDefaultSetupForCookie()
    {
        $instance = new CookieBuilder;
        $cookie = $instance->create('foo', 'bar');
        $this->assertEquals([
            'expires' => null,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => true,
        ], $cookie->getParameters());
    }


    /**
     * @covers Fracture\Http\CookieBuilder::__construct
    * @covers Fracture\Http\CookieBuilder::create
    * @covers Fracture\Http\CookieBuilder::setParameter
     */
    public function testResultWhenAlteringBuildersPamameters()
    {
        $instance = new CookieBuilder;
        $instance->setParameter('fake', 1);

        $cookie = $instance->create('foo', 'bar');
        $this->assertEquals([
            'expires' => null,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httpOnly' => true,
        ], $cookie->getParameters());

        $instance->setParameter('secure', true);

        $cookie = $instance->create('foo', 'bar');
        $this->assertEquals([
            'expires' => null,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httpOnly' => true,
        ], $cookie->getParameters());
    }
}
