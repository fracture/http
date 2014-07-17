<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class ContentDispositionTest extends PHPUnit_Framework_TestCase
{
    public function testParsingSimpleEntry()
    {
        $instance = new ContentDisposition('form-data; name="text"');
        $instance->prepare();

        $this->assertEquals('text', $instance->getAttribute('name'));
        $this->assertNull($instance->getAttribute('fake'));
    }


    public function testFileUploadEntry()
    {
        $instance = new ContentDisposition('form-data; name="file"; filename="file-simple.png"');
        $instance->prepare();

        $this->assertEquals('file', $instance->getAttribute('name'));
        $this->assertEquals('file-simple.png', $instance->getAttribute('filename'));
    }
}