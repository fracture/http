<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class FileCatalogBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\FileCatalogBuilder::__construct
     * @covers Fracture\Http\FileCatalogBuilder::create
     * @covers Fracture\Http\FileCatalogBuilder::createItem
     */
    public function testCreatedDummyElement()
    {
        $builder = $this->getMock('Fracture\Http\UploadedFileBuilder');

        $instance = new FileCatalogBuilder($builder);
        $object = $instance->create([]);

        $this->assertInstanceOf('Fracture\Http\FileCatalog', $object);
    }


    /**
     * @covers Fracture\Http\FileCatalogBuilder::__construct
     * @covers Fracture\Http\FileCatalogBuilder::create
     * @covers Fracture\Http\FileCatalogBuilder::createItem
     */
    public function testWithInvalidInput()
    {
        $input = [ 'foo' => 'bar' ];

        $response  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $response->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(false));


        $builder = $this->getMock('Fracture\Http\UploadedFileBuilder', ['create']);
        $builder->expects($this->once())
                ->method('create')
                ->will($this->returnValue($response));


        $instance = new FileCatalogBuilder($builder);

        $object = $instance->create($input);
        $this->assertInstanceOf('Fracture\Http\FileCatalog', $object);
    }


    /**
     * @covers Fracture\Http\FileCatalogBuilder::__construct
     * @covers Fracture\Http\FileCatalogBuilder::create
     * @covers Fracture\Http\FileCatalogBuilder::createItem
     */
    public function testWithInvalidMalformedUpload()
    {
        $input = ['foo' => ['bar']];

        $response  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $response->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(false));


        $builder = $this->getMock('Fracture\Http\UploadedFileBuilder', ['create']);
        $builder->expects($this->once())
                ->method('create')
                ->with($this->equalTo($input['foo']))
                ->will($this->returnValue($response));

        $instance = new FileCatalogBuilder($builder);

        $object = $instance->create($input);
        $this->assertInstanceOf('Fracture\Http\FileCatalog', $object);
    }

    /**
     * @covers Fracture\Http\FileCatalogBuilder::__construct
     * @covers Fracture\Http\FileCatalogBuilder::create
     * @covers Fracture\Http\FileCatalogBuilder::createItem
     */
    public function testSingleFileUpload()
    {
        $input = [
            'alpha' => [
                'name'      => 'simple.png',
                'type'      => 'image/png',
                'tmp_name'  => FIXTURE_PATH . '/file-simple.png',
                'error'     => 0,
                'size'      => 74,
            ],
        ];

        $response  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $response->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(true));


        $builder = $this->getMock('Fracture\Http\UploadedFileBuilder', ['create']);
        $builder->expects($this->once())
                ->method('create')
                ->with($this->equalTo($input['alpha']))
                ->will($this->returnValue($response));

        $instance = new FileCatalogBuilder($builder);
        $object = $instance->create($input);

        $this->assertInstanceOf('Fracture\Http\FileCatalog', $object);
        $this->assertInstanceOf('Fracture\Http\UploadedFile', $object['alpha']);
    }


    /**
     * @covers Fracture\Http\FileCatalogBuilder::__construct
     * @covers Fracture\Http\FileCatalogBuilder::create
     * @covers Fracture\Http\FileCatalogBuilder::createItem
     */
    public function testTwoFilesUploadedFromDiffrentInputs()
    {
        $input = [
            'alpha' => [
                'name'      => 'simple.png',
                'type'      => 'image/png',
                'tmp_name'  => FIXTURE_PATH . '/file-simple.png',
                'error'     => 0,
                'size'      => 74,
            ],
            'beta' => [
                'name'      => 'no-extension',
                'type'      => 'application/octet-stream',
                'tmp_name'  => FIXTURE_PATH . '/file-tempname',
                'error'     => 0,
                'size'      => 75,
            ],
        ];

        $alpha  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $alpha->expects($this->once())
              ->method('isValid')
              ->will($this->returnValue(true));

        $beta  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $beta->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(true));

        $builder = $this->getMock('Fracture\Http\UploadedFileBuilder', ['create']);
        $builder->expects($this->exactly(2))
                ->method('create')
                ->will($this->onConsecutiveCalls($alpha, $beta));

        $instance = new FileCatalogBuilder($builder);
        $object = $instance->create($input);

        $this->assertInstanceOf('Fracture\Http\FileCatalog', $object);
    }


    /**
     * @covers Fracture\Http\FileCatalogBuilder::__construct
     * @covers Fracture\Http\FileCatalogBuilder::create
     * @covers Fracture\Http\FileCatalogBuilder::createItem
     * @covers Fracture\Http\FileCatalogBuilder::createFromList
     */
    public function testTwoFilesUploadedFromInputWithSameName()
    {
        $input = [
            'alpha' => [
                'name'      => ['tempname', 'simple.png'],
                'type'      => ['application/octet-stream', 'image/png'],
                'tmp_name'  => [FIXTURE_PATH . '/file-tempname', FIXTURE_PATH . '/file-simple.png'],
                'error'     => [0, 0],
                'size'      => [75, 74],
            ],
        ];

        $response  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $response->expects($this->exactly(2))
                 ->method('isValid')
                 ->will($this->returnValue(true));


        $builder = $this->getMock('Fracture\Http\UploadedFileBuilder', ['create']);
        $builder->expects($this->exactly(2))
                ->method('create')
                ->will($this->returnValue($response));

        $instance = new FileCatalogBuilder($builder);
        $object = $instance->create($input);

        $this->assertInstanceOf('Fracture\Http\FileCatalog', $object);
        $this->assertInstanceOf('Fracture\Http\FileCatalog', $object['alpha']);
        $this->assertInstanceOf('Fracture\Http\UploadedFile', $object['alpha'][0]);
        $this->assertInstanceOf('Fracture\Http\UploadedFile', $object['alpha'][1]);
    }
}
