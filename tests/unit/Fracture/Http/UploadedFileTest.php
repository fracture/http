<?php

namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class UploadedFileTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provideSimpleTypes
     *
     * @covers Fracture\Http\UploadedFile::__construct
     * @covers Fracture\Http\UploadedFile::getMimeType
     * @covers Fracture\Http\UploadedFile::prepare
     * @covers Fracture\Http\UploadedFile::hasProperExtension
     * @covers Fracture\Http\UploadedFile::isDubious
     * @covers Fracture\Http\UploadedFile::getPath
     */
    public function testUploadTypes($params, $type, $validity)
    {

        $instance = $this->getMock('Fracture\Http\UploadedFile', ['seemsTampered'], [$params]);
        $instance->expects($this->once())
                 ->method('seemsTampered')
                 ->will($this->returnValue(false));

        $instance->prepare();
        $this->assertEquals($type, $instance->getMimeType());
        $this->assertEquals($validity, $instance->hasProperExtension());
    }

    public function provideSimpleTypes()
    {
        return include FIXTURE_PATH . '/uploads-type.php';
    }

    /**
     * @dataProvider provideSimpleValidationList
     *
     * @covers Fracture\Http\UploadedFile::__construct
     * @covers Fracture\Http\UploadedFile::isValid
     * @covers Fracture\Http\UploadedFile::prepare
     * @covers Fracture\Http\UploadedFile::isDubious
     * @covers Fracture\Http\UploadedFile::getPath
     */
    public function testUploadValidity($params, $result)
    {
        $instance = $this->getMock('Fracture\Http\UploadedFile', ['seemsTampered'], [$params]);
        $instance->expects($this->any())
                 ->method('seemsTampered')
                 ->will($this->returnValue(false));


        $instance->prepare();
        $this->assertEquals($result, $instance->isValid());
    }


    public function provideSimpleValidationList()
    {
        return include FIXTURE_PATH . '/uploads-validity.php';
    }


    /**
     * @dataProvider provideUploadExtensions
     *
     * @covers Fracture\Http\UploadedFile::__construct
     * @covers Fracture\Http\UploadedFile::getExtension
     */
    public function testUploadExtensions($params, $result)
    {
        $instance = new UploadedFile($params);
        $this->assertEquals($result, $instance->getExtension());
    }


    public function provideUploadExtensions()
    {
        return include FIXTURE_PATH . '/uploads-extensions.php';
    }

    /**
     * @covers Fracture\Http\UploadedFile::__construct
     * @covers Fracture\Http\UploadedFile::getName
     * @covers Fracture\Http\UploadedFile::getMimeType
     * @covers Fracture\Http\UploadedFile::getSize
     */
    public function testSimpleGetters()
    {
        $params = [
            'name'      => 'simple.png',
            'type'      => 'image/png',
            'tmp_name'  => FIXTURE_PATH . '/files/simple.png',
            'error'     => UPLOAD_ERR_OK,
            'size'      => 74,
        ];

        $instance = new UploadedFile($params);

        $this->assertEquals($params['name'], $instance->getName());
        $this->assertEquals($params['type'], $instance->getMimeType());
        $this->assertEquals($params['size'], $instance->getSize());
    }
}
