<?php


namespace Fracture\Http\Parsers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class  DataFormTest extends PHPUnit_Framework_TestCase
{

    public function testEmptyInput()
    {
        $instance = new DataForm('');
        $instance->prepare();

        $this->assertNull($instance->getParameter('foobar'));
    }
}
