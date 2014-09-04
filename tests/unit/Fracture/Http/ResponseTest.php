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
     * @covers Fracture\Http\Response::getBody
     * @covers Fracture\Http\Response::setStatusCode
     * @covers Fracture\Http\Response::getStatusCode
     */
    public function testProperStatusCode()
    {
        $instance = new Response;
        $instance->setStatusCode(404);
        $this->assertSame(404, $instance->getStatusCode());
    }


    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Fracture\Http\Response::getBody
     * @covers Fracture\Http\Response::setStatusCode
     */
    public function testBadStatusCode()
    {
        $instance = new Response;
        $instance->setStatusCode(9999);
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

        $instance->setBody('sit');
        $this->assertSame('sit', $instance->getBody());

        $instance->appendBody(' dolor amet');
        $this->assertSame('sit dolor amet', $instance->getBody());

        $instance->prependBody('lorem ipsum ');
        $this->assertSame('lorem ipsum sit dolor amet', $instance->getBody());

        $instance->setBody(null);
        $this->assertNull($instance->getBody());
    }


    /**
     * @covers Fracture\Http\Response::addHeader
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testSimpleHeader()
    {
        $header = $this->getMock('Fracture\Http\Headers\ContentType', ['getName', 'getValue']);
        $header->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('Alpha'));

        $header->expects($this->any())
               ->method('getValue')
               ->will($this->returnValue('beta'));


        $instance = new Response;
        $instance->addHeader($header);

        $this->assertEquals([
            'Alpha: beta',
        ], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::addHeader
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testReplacingHeaderInstance()
    {
        $original = $this->getMock('Fracture\Http\Headers\ContentType', ['getName', 'getValue']);
        $original->expects($this->any())
                 ->method('getName')
                 ->will($this->returnValue('Alpha'));

        $original->expects($this->any())
                 ->method('getValue')
                 ->will($this->returnValue('beta'));

        $replacement = $this->getMock('Fracture\Http\Headers\ContentType', ['getName', 'getValue']);
        $replacement->expects($this->any())
                    ->method('getName')
                    ->will($this->returnValue('Alpha'));

        $replacement->expects($this->any())
                    ->method('getValue')
                    ->will($this->returnValue('gamma'));


        $instance = new Response;
        $instance->addHeader($original);
        $instance->addHeader($replacement);


        $this->assertEquals([
            'Alpha: gamma',
        ], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::addCookie
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testCookieAsHeader()
    {
        $cookie = $this->getMock('Fracture\Http\Cookie', ['getName', 'getHeaderValue'], [], '', false);
        $cookie->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('alpha'));
        $cookie->expects($this->any())
               ->method('getHeaderValue')
               ->will($this->returnValue('alpha=omega; HttpOnly'));

        $instance = new Response;
        $instance->addCookie($cookie);

        $this->assertEquals([
            'Set-Cookie: alpha=omega; HttpOnly',
        ], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::addCookie
     * @covers Fracture\Http\Response::removeCookie
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testHeaderAfterRemovingCookie()
    {
        $cookie = $this->getMock('Fracture\Http\Cookie', ['getName'], [], '', false);
        $cookie->expects($this->any())
               ->method('getName')
               ->will($this->returnValue('alpha'));

        $instance = new Response;
        $instance->addCookie($cookie);

        $instance->removeCookie('alpha');

        $this->assertEquals([], $instance->getHeaders());
    }



    /**
     * @covers Fracture\Http\Response::addCookie
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testMultipleCookiesAsHeader()
    {
        $cookie = $this->getMock('Fracture\Http\Cookie', ['getName', 'getHeaderValue'], [], '', false);
        $cookie->expects($this->any())
               ->method('getName')
               ->will($this->onConsecutiveCalls('alpha', 'beta'));
        $cookie->expects($this->any())
               ->method('getHeaderValue')
               ->will($this->onConsecutiveCalls('alpha=1; HttpOnly', 'beta=2; HttpOnly'));

        $instance = new Response;
        $instance->addCookie($cookie);
        $instance->addCookie($cookie);

        $this->assertEquals([
            'Set-Cookie: alpha=1; HttpOnly',
            'Set-Cookie: beta=2; HttpOnly',
        ], $instance->getHeaders());
    }

}
