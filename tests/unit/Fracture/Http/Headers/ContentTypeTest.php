<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ContentTypeTest extends PHPUnit_Framework_TestCase
{


    /**
     * @covers Fracture\Http\Headers\ContentType::__construct
     * @covers Fracture\Http\Headers\ContentType::extractData
     */
    public function testEmptyInstance()
    {
        $instance = new ContentType;
        $this->assertEquals([], $instance->extractData(''));
        $this->assertEquals(
            ['value' => 'application/json'],
            $instance->extractData('application/json')
        );
        $this->assertEquals(
            ['value' => 'application/json', 'version' => '1'],
            $instance->extractData('application/json;version=1')
        );
        $this->assertEquals(
            ['value' => 'text/html', 'charset' => 'utf-8'],
            $instance->extractData('text/html; charset=utf-8')
        );
    }


    /**
     * @covers Fracture\Http\Headers\ContentType::__construct
     * @covers Fracture\Http\Headers\ContentType::prepare
     * @covers Fracture\Http\Headers\ContentType::contains
     */
    public function testPreparedResult()
    {
        $instance = new ContentType('text/html');
        $instance->prepare();

        $this->assertTrue($instance->contains('text/html'));
        $this->assertFalse($instance->contains('image/png'));
    }


    /**
     * @covers Fracture\Http\Headers\ContentType::__construct
     * @covers Fracture\Http\Headers\ContentType::prepare
     * @covers Fracture\Http\Headers\ContentType::contains
     * @covers Fracture\Http\Headers\ContentType::setALternativeValue
     */
    public function testPreparedResultAterManualAlteration()
    {
        $instance = new ContentType('application/json');
        $instance->setALternativeValue('image/png');
        $instance->prepare();

        $this->assertTrue($instance->contains('image/png'));
        $this->assertFalse($instance->contains('text/html'));
    }
}
