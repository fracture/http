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
}