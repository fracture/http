<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class HeaderFactoryTest extends PHPUnit_Framework_TestCase
{





    /**
     * @covers Fracture\Http\HeaderFactory::splitEntry
     */
    public function testSplittingBadValue()
    {
        $factory = new HeaderFactory;
        $this->assertFalse($factory->splitEntry('Random text'));
    }


    /**
     * @covers Fracture\Http\HeaderFactory::splitEntry
     */
    public function testSimpleHeaderSplitting()
    {
        $factory = new HeaderFactory;
        $this->assertEquals([
            'Content-Type',
            'text/plain',
        ], $factory->splitEntry('Content-Type: text/plain'));
    }



    /**
     * @covers Fracture\Http\HeaderFactory::create
     */
    public function testValuelessHeader()
    {
        $factory = new HeaderFactory;
        $this->assertNull($factory->create('Muahahahaha'));
    }


    /**
     * @covers Fracture\Http\HeaderFactory::create
     */
    public function testMissingHeaderCreation()
    {
        $factory = new HeaderFactory;
        $this->assertNull($factory->create('Bad: cookie'));
    }


    /**
     * @dataProvider provideInstantiatedHeader
     *
     * @covers Fracture\Http\HeaderFactory::create
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
                'expected' => '\Fracture\Http\Headers\Location',
                'parameter' => 'Location: /alpha/beta',
            ],
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
