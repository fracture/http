<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ContentDispositionTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Headers\ContentDisposition::__construct
     * @covers Fracture\Http\Headers\ContentDisposition::prepare
     * @covers Fracture\Http\Headers\ContentDisposition::extractData
     * @covers Fracture\Http\Headers\ContentDisposition::getAttribute
     */
    public function testParsingSimpleEntry()
    {
        $instance = new ContentDisposition('form-data; name="text"');
        $instance->prepare();

        $this->assertEquals('text', $instance->getAttribute('name'));
        $this->assertNull($instance->getAttribute('fake'));
    }


    /**
     * @covers Fracture\Http\Headers\ContentDisposition::__construct
     * @covers Fracture\Http\Headers\ContentDisposition::prepare
     * @covers Fracture\Http\Headers\ContentDisposition::extractData
     * @covers Fracture\Http\Headers\ContentDisposition::getAttribute
     */
    public function testFileUploadEntry()
    {
        $instance = new ContentDisposition('form-data; name="file"; filename="file-simple.png"');
        $instance->prepare();

        $this->assertEquals('file', $instance->getAttribute('name'));
        $this->assertEquals('file-simple.png', $instance->getAttribute('filename'));
    }


    /**
     * @covers Fracture\Http\Headers\ContentDisposition::__construct
     * @covers Fracture\Http\Headers\ContentDisposition::getName
     */
    public function testGivenName()
    {
        $instance = new ContentDisposition;
        $this->assertSame('Content-Disposition', $instance->getName());
    }
}
