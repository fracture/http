<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class LocationTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Headers\Location::__construct
     * @covers Fracture\Http\Headers\Location::prepare
     * @covers Fracture\Http\Headers\Location::getParsedData
     *
     * @covers Fracture\Http\Headers\Location::extractData
     */
    public function testEmptyInstance()
    {
        $instance = new Location('http://example.tld/path');
        $instance->prepare();
        $this->assertEquals([
            'scheme' => 'http',
            'host' => 'example.tld',
            'path' => '/path',
        ], $instance->getParsedData());
    }
}
