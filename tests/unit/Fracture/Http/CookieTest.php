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
     * @covers Fracture\Http\Cookie::hasInvalidOptions
     * @covers Fracture\Http\Cookie::setOptions
     * @covers Fracture\Http\Cookie::cleanOptions
     * @covers Fracture\Http\Cookie::getOptions
     */
    public function testChangingCookieOptions()
    {
        $instance = new Cookie('name', 'value');
        $instance->setOptions([
            'expires' => '123',
            'path' => '/foo',
            'domain' => 'test.com',
            'secure' => true,
            'httpOnly' => false,
        ]);


        $this->assertEquals([
            'expires' => '123',
            'path' => '/foo',
            'domain' => 'test.com',
            'secure' => true,
            'httpOnly' => false,
        ], $instance->getOptions());
    }



    /**
     * @covers Fracture\Http\Cookie::__construct
     * @covers Fracture\Http\Cookie::hasInvalidOptions
     * @covers Fracture\Http\Cookie::setOptions
     * @covers Fracture\Http\Cookie::cleanOptions
     * @covers Fracture\Http\Cookie::getOptions
     */
    public function testChangingCookieOptionsToBadValues()
    {
        $instance = new Cookie('name', 'value');
        $instance->setOptions([
            'expires' => 'aaaa',
            'path' => null,
            'domain' => 'FOO.BAR',
            'secure' => 0,
            'httpOnly' => 'true',
        ]);


        $this->assertEquals([
            'expires' => 0,
            'path' => '/',
            'domain' => 'foo.bar',
            'secure' => false,
            'httpOnly' => true,
        ], $instance->getOptions());
    }




    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     *
     * @covers Fracture\Http\Cookie::__construct
     * @covers Fracture\Http\Cookie::setOptions
     * @covers Fracture\Http\Cookie::hasInvalidOptions
     */
    public function testBadCookieOptions()
    {
        $instance = new Cookie('name', 'value');
        $instance->setOptions([
            'bad' => 'value',
        ]);
    }


    /**
     * @covers Fracture\Http\Cookie::__construct
     * @covers Fracture\Http\Cookie::getHeaderValue
     * @covers Fracture\Http\Cookie::collectFormatedOptions
     */
    public function testHeaderStringFormation()
    {
        $instance = new Cookie('name', 'value');
        $this->assertSame('name=value; HttpOnly', $instance->getHeaderValue());
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
