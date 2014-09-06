<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class SetCookieTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Headers\SetCookie::__construct
     * @covers Fracture\Http\Headers\SetCookie::isFinal
     */
    public function testIsFinalResponse()
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', [], [], '', false);

        $instance = new SetCookie($cookieMock);
        $this->assertTrue($instance->isFinal());
    }


    /**
     * @covers Fracture\Http\Headers\SetCookie::__construct
     * @covers Fracture\Http\Headers\SetCookie::getName
     */
    public function testName()
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', [], [], '', false);

        $instance = new SetCookie($cookieMock);
        $this->assertSame('Set-Cookie', $instance->getName());
    }


    /**
     * @covers Fracture\Http\Headers\SetCookie::__construct
     * @covers Fracture\Http\Headers\SetCookie::getName
     */
    public function testHeaderValue()
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', [], [], '', false);
        // $cookieMock->expects($this->once())
        //            ->method('getName')
        //            ->will($this->returnValue('alpha'));
        // $cookieMock->expects($this->once())
        //            ->method('getValue')
        //            ->will($this->returnValue('beta'));

        $instance = new SetCookie($cookieMock);
        //$this->assertSame('Set-Cookie: alpha=beta; HttpOnly', $instance->getValue());
    }
}
