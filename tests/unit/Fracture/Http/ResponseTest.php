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
     *
     * @covers Fracture\Http\Response::populateHeaderList
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
            [
                'value' => 'Alpha: beta',
                'replace' => true,
            ],
        ], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::addHeader
     * @covers Fracture\Http\Response::getHeaders
     *
     * @covers Fracture\Http\Response::populateHeaderList
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
            [
                'value' => 'Alpha: gamma',
                'replace' => true,
            ],
        ], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::addHeader
     * @covers Fracture\Http\Response::getHeaders
     *
     * @covers Fracture\Http\Response::populateHeaderList
     * @covers Fracture\Http\Response::adjustForHostname
     */
    public function testSimpleLocationHeaderAddition()
    {
        $headerMock = $this->getMock('Fracture\Http\Headers\Location', ['getName', 'getValue']);
        $headerMock->expects($this->any())
                   ->method('getName')
                   ->will($this->returnValue('Location'));

        $headerMock->expects($this->any())
                   ->method('getValue')
                   ->will($this->returnValue('beta'));

        $instance = new Response;
        $instance->addHeader($headerMock);

        $this->assertEquals([
            [
                'value' => 'Location: beta',
                'replace' => true,
            ],
        ], $instance->getHeaders());

        $this->assertSame(302, $instance->getStatusCode());
    }


    /**
     * @covers Fracture\Http\Response::addHeader
     * @covers Fracture\Http\Response::getHeaders
     * @covers Fracture\Http\Response::setHostname
     *
     * @covers Fracture\Http\Response::populateHeaderList
     * @covers Fracture\Http\Response::adjustForHostname
     *
     * @dataProvider provideLocationHeaderAdditionWithHostnameSet
     */
    public function testLocationHeaderAdditionWithHostnameSet($host, $path, $expected)
    {
        $headerMock = $this->getMock('Fracture\Http\Headers\Location', ['getName', 'getValue']);
        $headerMock->expects($this->any())
                   ->method('getName')
                   ->will($this->returnValue('Location'));

        $headerMock->expects($this->any())
                   ->method('getValue')
                   ->will($this->returnValue($path));

        $instance = new Response;
        $instance->addHeader($headerMock);

        $instance->setHostname($host);

        $this->assertEquals([
            [
                'value' => $expected,
                'replace' => true,
            ],
        ], $instance->getHeaders());
    }


    public function provideLocationHeaderAdditionWithHostnameSet()
    {
        return [
            [
                'host' => 'http://example.tld',
                'path' => '/beta',
                'expected' => 'Location: http://example.tld/beta',
            ],
            [
                'host' => 'https://example.tld',
                'path' => '/beta',
                'expected' => 'Location: https://example.tld/beta',
            ],
            [
                'host' => 'http://example.tld',
                'path' => 'http://domain.tld/beta',
                'expected' => 'Location: http://domain.tld/beta',
            ],
        ];
    }

    /**
     * @covers Fracture\Http\Response::removeCookie
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testSimpleRemoveCookie()
    {
        $instance = new Response;
        $instance->removeCookie('name');

        $this->assertEquals([
            [
                'value' => 'Set-Cookie: name=deleted; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; HttpOnly',
                'replace' => false,
            ],
        ], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::removeCookie
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testMoreComplicatedSimpleRemoveCookie()
    {
        $instance = new Response;
        $instance->removeCookie('name', [
            'path' => '/test',
            'expires' => 1,
        ]);

        $this->assertEquals([
            [
                'value' => 'Set-Cookie: name=deleted; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/test; HttpOnly',
                'replace' => false,
            ],
        ], $instance->getHeaders());
    }


    /**
     * @covers Fracture\Http\Response::addCookie
     * @covers Fracture\Http\Response::getHeaders
     */
    public function testAddCookie()
    {
        $instance = new Response;
        $instance->addCookie('alpha', 'beta');

        $this->assertEquals([
            [
                'value' => 'Set-Cookie: alpha=beta; Path=/; HttpOnly',
                'replace' => false,
            ],
        ], $instance->getHeaders());
    }



}
