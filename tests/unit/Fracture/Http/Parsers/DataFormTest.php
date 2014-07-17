<?php


namespace Fracture\Http\Parsers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class DataFormTest extends PHPUnit_Framework_TestCase
{

    public function testWithoutInputs()
    {
        $input = FIXTURE_PATH . '/data-form/input-01.txt';

        $instance = new DataForm(new \Fracture\Http\HeaderFactory, $input);
        $instance->prepare();

        $this->assertNull($instance->getParameter('foobar'));
    }


    public function testSingleInput()
    {
        $input = FIXTURE_PATH . '/data-form/input-01.txt';
        $boundry = 'WebKitFormBoundaryDPzbv2se5E43jOM4';

        $instance = new DataForm(new \Fracture\Http\HeaderFactory, $input, $boundry);
        $instance->prepare();

        $this->assertSame('value', $instance->getParameter('parameter'));
    }


    public function testMultipleInputs()
    {
        $input = FIXTURE_PATH . '/data-form/input-08.txt';
        $boundry = 'WebKitFormBoundaryWYfoSzZ7Ie4Sqsef';

        $instance = new DataForm(new \Fracture\Http\HeaderFactory, $input, $boundry);
        $instance->prepare();

        $this->assertSame('one', $instance->getParameter('one'));
        $this->assertSame('22222', $instance->getParameter('two'));
    }


    public function testSingleFileUpload()
    {
        $input = FIXTURE_PATH . '/data-form/input-03.txt';
        $boundry = 'WebKitFormBoundarysrP3vUDVYcT3Bhcs';

        $instance = new DataForm(new \Fracture\Http\HeaderFactory, $input, $boundry);
        $instance->prepare();

        // $this->assertSame('value', $instance->getParameter('parameter')); #TODO
    }
}
