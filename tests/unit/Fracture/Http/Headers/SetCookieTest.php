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
    public function testHeaderName()
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', [], [], '', false);

        $instance = new SetCookie($cookieMock);
        $this->assertSame('Set-Cookie', $instance->getName());
    }

    /**
     * @covers Fracture\Http\Headers\SetCookie::prepare
     * @covers Fracture\Http\Headers\SetCookie::hasInvalidOptions
     *
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testInvalidOptions()
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', [], [], '', false);
        $instance = new SetCookie($cookieMock, ['foo' => 'bar']);
        $instance->prepare();
    }


    /**
     * @covers Fracture\Http\Headers\SetCookie::__construct
     * @covers Fracture\Http\Headers\SetCookie::prepare
     * @covers Fracture\Http\Headers\SetCookie::getValue
     *
     * @covers Fracture\Http\Headers\SetCookie::hasInvalidOptions
     * @covers Fracture\Http\Headers\SetCookie::cleanOptions
     * @covers Fracture\Http\Headers\SetCookie::hasInvalidOptions
     * @covers Fracture\Http\Headers\SetCookie::collectFormatedOptions
     * @covers Fracture\Http\Headers\SetCookie::collectExpireTime
     * @covers Fracture\Http\Headers\SetCookie::collectDomainPathValue
     * @covers Fracture\Http\Headers\SetCookie::collectBooleanOptions
     *
	 * @dataProvider provideHeaderValue
     */
    public function testHeaderValue($options, $expected)
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', ['getName', 'getValue'], [], '', false);
        $cookieMock->expects($this->once())
                   ->method('getName')
                   ->will($this->returnValue('alpha'));
        $cookieMock->expects($this->once())
                   ->method('getValue')
                   ->will($this->returnValue('beta'));

       $instance = new SetCookie($cookieMock, $options);
       $instance->prepare();

       $this->assertSame($expected, $instance->getValue());
    }


    public function provideHeaderValue()
    {
        return [
            [
                'options' => [],
                'expected' => 'alpha=beta; Path=/; HttpOnly',
            ],
            [
                'options' => ['httpOnly' => false],
                'expected' => 'alpha=beta; Path=/',
            ],
            [
                'options' => ['secure' => true],
                'expected' => 'alpha=beta; Path=/; Secure; HttpOnly',
            ],
            [
                'options' => ['path' => '/gamma'],
                'expected' => 'alpha=beta; Path=/gamma; HttpOnly',
            ],
            [
                'options' => ['path' => null],
                'expected' => 'alpha=beta; Path=/; HttpOnly',
            ],
            [
                'options' => ['domain' => '.test.com'],
                'expected' => 'alpha=beta; Domain=.test.com; Path=/; HttpOnly',
            ],
            [
                'options' => ['domain' => 'site.tld', 'path' => '/gamma'],
                'expected' => 'alpha=beta; Domain=site.tld; Path=/gamma; HttpOnly',
            ],
            [
                'options' => ['expires' => 1410269554],
                'expected' => 'alpha=beta; Expires=Tue, 09 Sep 2014 13:32:34 GMT; Path=/; HttpOnly',
            ],
            [
                'options' => ['expires' => 0],
                'expected' => 'alpha=beta; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; HttpOnly',
            ],
            [
                'options' => ['expires' => 'bad value'],
                'expected' => 'alpha=beta; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; HttpOnly',
            ],
        ];
    }


    /**
     * @covers Fracture\Http\Headers\SetCookie::__construct
     * @covers Fracture\Http\Headers\SetCookie::prepare
     * @covers Fracture\Http\Headers\SetCookie::getValue
     *
     * @covers Fracture\Http\Headers\SetCookie::collectExpireTime
     * @covers Fracture\Http\Headers\SetCookie::isDateTime
     * @covers Fracture\Http\Headers\SetCookie::convertTime
     *
     * @dataProvider provideHeaderExpireValues
     */
    public function testHeaderExpireValue($options, $expected)
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', ['getName', 'getValue'], [], '', false);
        $cookieMock->expects($this->once())
                   ->method('getName')
                   ->will($this->returnValue('alpha'));
        $cookieMock->expects($this->once())
                   ->method('getValue')
                   ->will($this->returnValue('beta'));

       $instance = new SetCookie($cookieMock, $options);
       $instance->prepare();

       $this->assertSame($expected, $instance->getValue());
    }


    public function provideHeaderExpireValues()
    {
        return [
            [
                'options' => ['expires' => 1410269554],
                'expected' => 'alpha=beta; Expires=Tue, 09 Sep 2014 13:32:34 GMT; Path=/; HttpOnly',
            ],
            [
                'options' => ['expires' => 0],
                'expected' => 'alpha=beta; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; HttpOnly',
            ],
            [
                'options' => ['expires' => 'bad value'],
                'expected' => 'alpha=beta; Expires=Thu, 01 Jan 1970 00:00:00 GMT; Path=/; HttpOnly',
            ],
        ];
    }

    /**
     * @covers Fracture\Http\Headers\SetCookie::prepare
     * @covers Fracture\Http\Headers\SetCookie::isDateTime
     * @covers Fracture\Http\Headers\SetCookie::convertTime
     *
     * @dataProvider provideeaderExpireValueAsDateTime
     */
    public function testHeaderExpireValueAsDateTime($string, $expected)
    {
        $cookieMock = $this->getMock('Fracture\Http\Cookie', ['getName', 'getValue'], [], '', false);
        $cookieMock->expects($this->once())
                   ->method('getName')
                   ->will($this->returnValue('alpha'));
        $cookieMock->expects($this->once())
                   ->method('getValue')
                   ->will($this->returnValue('beta'));

       $instance = new SetCookie($cookieMock, [
           'expires' => new \DateTime($string),
       ]);
       $instance->prepare();

       $this->assertSame($expected, $instance->getValue());
    }


    public function provideeaderExpireValueAsDateTime()
    {
        return [
            [
                'string' => 'Tue, 09 Sep 2014 13:32:34 GMT',
                'expected' => 'alpha=beta; Expires=Tue, 09 Sep 2014 13:32:34 GMT; Path=/; HttpOnly',
            ],
            [
                'string' => 'Tue, 09 Sep 2014 13:00:00 +0000',
                'expected' => 'alpha=beta; Expires=Tue, 09 Sep 2014 13:00:00 GMT; Path=/; HttpOnly',
            ],
            [
                'string' => 'Tue, 09 Sep 2014 15:00:00 +0200',
                'expected' => 'alpha=beta; Expires=Tue, 09 Sep 2014 13:00:00 GMT; Path=/; HttpOnly',
            ],
        ];
    }
}
