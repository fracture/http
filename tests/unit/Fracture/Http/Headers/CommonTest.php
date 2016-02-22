<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class CommonTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::isFinal
     */
    public function testIsFinalResponse()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common');
        $this->assertFalse($instance->isFinal());
    }

    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::getValue
     */
    public function testDefaultInstantiation()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common');
        $this->assertSame('', $instance->getValue());
    }

    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::getValue
     */
    public function testStandardInstantiation()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common', ['alpha']);
        $this->assertSame('alpha', $instance->getValue());
    }


    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::setValue
     * @covers Fracture\Http\Headers\Common::getValue
     */
    public function testStandardAlteration()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common', ['']);
        $instance->setValue('beta');
        $this->assertSame('beta', $instance->getValue());
    }


    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::getName
     */
    public function testNameValue()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common', ['']);
        $this->assertSame('Unspecified', $instance->getName());
    }


    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::getParsedData
     */
    public function testParsedDataForUnpreparedInstance()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common');
        $this->assertNull($instance->getParsedData());
    }


    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::prepare
     * @covers Fracture\Http\Headers\Common::getParsedData
     */
    public function testParsedDataForPreparedInstanceWithNoValueSet()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common');
        $instance->prepare();
        $this->assertNull($instance->getParsedData());
    }


    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::prepare
     * @covers Fracture\Http\Headers\Common::getParsedData
     */
    public function testParsedDataForPreparedInstance()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common', ['alpha']);
        $instance->expects($this->any())
                 ->method('extractData')
                 ->will($this->returnValue('beta'));

        $instance->prepare();
        $this->assertSame('beta', $instance->getParsedData());
    }


    /**
     * @covers Fracture\Http\Headers\Common::__construct
     * @covers Fracture\Http\Headers\Common::prepare
     * @covers Fracture\Http\Headers\Common::getParsedData
     * @covers Fracture\Http\Headers\Common::getParameter
     */
    public function testValueRetrieval()
    {
        $instance = $this->getMockForAbstractClass('Fracture\Http\Headers\Common', ['type/subtype; name=4']);
        $instance->expects($this->any())
                 ->method('extractData')
                 ->will($this->returnValue(['name' => 4]));

        $instance->prepare();
        $this->assertSame(4, $instance->getParameter('name'));
    }
}
