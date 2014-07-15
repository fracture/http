<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class HeaderFactoryTest extends PHPUnit_Framework_TestCase
{

    public function testSplittingBadValue()
    {
        $factory = new HeaderFactory;
        $this->assertNull($factory->splitEntry('Random text'));
    }


    public function testSimpleHeaderSplitting()
    {
        $factory = new HeaderFactory;
        $this->assertEquals([
            'Content-Type',
            'text/plain',
        ], $factory->splitEntry('Content-Type: text/plain'));
    }


    public function testMissingHeaderCreation()
    {
        $factory = new HeaderFactory;
        $this->assertFalse($factory->create('Bad: cookie'));
    }


    /**
     * @dataProvider provideInstantiatedHeader
     */
    public function testInstantiatedHeader($expected, $parameter)
    {
        $factory = new HeaderFactory;
        $this->assertInstanceOf('' . $expected, $factory->create($parameter));
    }


    public function provideInstantiatedHeader()
    {
        return [
            [
                'expected' => '\Fracture\Http\Headers\ContentType',
                'parameter' => 'Content-Type: text/plain',
            ],
            [
                'expected' => '\Fracture\Http\Headers\ContentDisposition',
                'parameter' => 'Content-Disposition: form-data; name="text"',
            ],
        ];
    }
}
