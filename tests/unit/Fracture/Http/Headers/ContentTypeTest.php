<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ContentTypeTest extends PHPUnit_Framework_TestCase
{


    /**
     * @covers Fracture\Http\Headers\ContentType::__construct
     * @covers Fracture\Http\Headers\ContentType::getParsedData
     */
    public function testEmptyInstance()
    {
        $instance = new ContentType;
        $instance->prepare();
        $this->assertEquals([], $instance->getParsedData());
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
     * @covers Fracture\Http\Headers\ContentType::setValue
     */
    public function testPreparedResultAterManualAlteration()
    {
        $instance = new ContentType('application/json');
        $instance->setValue('image/png');
        $instance->prepare();

        $this->assertTrue($instance->contains('image/png'));
        $this->assertFalse($instance->contains('text/html'));
    }


    /**
     * @dataProvider provideVariousInputs
     *
     * @covers Fracture\Http\Headers\ContentType::__construct
     * @covers Fracture\Http\Headers\ContentType::setValue
     * @covers Fracture\Http\Headers\ContentType::prepare
     * @covers Fracture\Http\Headers\ContentType::getParsedData
     */
    public function testVariousInputs($expected, $parameter)
    {
        $instance = new ContentType;
        $instance->setValue($parameter);
        $instance->prepare();

        $this->assertEquals($expected, $instance->getParsedData());
    }


    public function provideVariousInputs()
    {
        return [
            [
                'expected' => ['value' => 'application/json'],
                'data' => 'application/json',
            ],
            [
                'expected' => ['value' => 'application/json', 'version' => '1'],
                'data' => 'application/json;version=1',
            ],
            [
                'expected' => ['value' => 'text/html', 'charset' => 'utf-8'],
                'data' => 'text/html; charset=utf-8',
            ],
            [
                'expected' => ['value' => 'multipart/form-data', 'boundary' => 'AaB03x'],
                'data' => 'multipart/form-data; boundary=AaB03x',
            ],
        ];
    }
}
