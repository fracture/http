<?php


namespace Fracture\Http\Parsers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class DataFormTest extends PHPUnit_Framework_TestCase
{

    public function testWithoutInputs()
    {
        $instance = new DataForm('');
        $instance->prepare();

        $this->assertNull($instance->getParameter('foobar'));
    }


    public function testSingleInput()
    {
        $input = FIXTURE_PATH . '/data-form/input-01.txt';
        $boundry = 'WebKitFormBoundaryDPzbv2se5E43jOM4';

        $instance = new DataForm($input, $boundry);
        $instance->prepare();

        $this->assertSame('value', $instance->getParameter('parameter'));
    }
}
