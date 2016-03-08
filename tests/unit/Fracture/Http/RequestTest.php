<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Request::setMethod
     * @covers Fracture\Http\Request::getMethod
     */
    public function testMethodGetterForRequest()
    {
        $request = new Request;
        $request->setMethod('GET');

        $this->assertEquals('get', $request->getMethod());
    }


    /**
     * @covers Fracture\Http\Request::getParameter
     */
    public function testParameterGetterWhenNoValue()
    {
        $request = new Request;
        $this->assertNull($request->getParameter('foobar'));
    }


    /**
     * @covers Fracture\Http\Request::setParameters
     * @covers Fracture\Http\Request::getParameter
     */
    public function testParameterGetterWhenSetValue()
    {
        $request = new Request;
        $request->setParameters(['param' => 'value']);
        $this->assertEquals('value', $request->getParameter('param'));
    }


    /**
     * @covers Fracture\Http\Request::setParameters
     * @covers Fracture\Http\Request::getParameter
     */
    public function testParameterGetterWithDifferentSetter()
    {
        $request = new Request;
        $request->setParameters(['param' => 'value']);
        $this->assertNull($request->getParameter('different'));
    }


    /**
     * @covers Fracture\Http\Request::setParameters
     */
    public function testDuplicateKeysAssignedToParameters()
    {
        set_error_handler([$this, 'handleWarnedMethod'], \E_USER_WARNING);

        $request = new Request;
        $request->setParameters(['alpha' => 'foo']);
        $request->setParameters(['alpha' => 'foo']);

        restore_error_handler();
    }

    public function handleWarnedMethod($errno, $errstr)
    {
         $this->assertEquals(\E_USER_WARNING, $errno);
    }



    /**
     * @dataProvider provideCleanUriList
     * @covers Fracture\Http\Request::setUri
     * @covers Fracture\Http\Request::getUri
     *
     * @covers Fracture\Http\Request::sanitizeUri
     * @covers Fracture\Http\Request::resolveUri
     * @covers Fracture\Http\Request::adjustUriSegments
     */
    public function testValidCleanUri($uri, $expected)
    {
        $request = new Request;
        $request->setUri($uri);

        $this->assertEquals($expected, $request->getUri());
    }


    public function provideCleanUriList()
    {
        return include FIXTURE_PATH . '/uri-variations.php';
    }


    /**
     * @covers Fracture\Http\Request::setAddress
     * @covers Fracture\Http\Request::getAddress
     */
    public function testValidAddress()
    {
        $request = new Request;
        $request->setAddress('127.0.0.1');

        $this->assertEquals('127.0.0.1', $request->getAddress());
    }

    /**
     * @covers Fracture\Http\Request::setAddress
     * @covers Fracture\Http\Request::getAddress
     */
    public function testInvalidAddress()
    {
        $request = new Request;
        $request->setAddress('a.b.c.d.e');

        $this->assertNull($request->getAddress());
    }


    /**
     * @covers Fracture\Http\Request::__construct
     * @covers Fracture\Http\Request::getUpload
     */
    public function testGatheringUploadsWithoutFiles()
    {
        $instance = new Request;
        $this->assertNull($instance->getUpload('foobar'));
    }


    /**
     * @covers Fracture\Http\Request::__construct
     * @covers Fracture\Http\Request::setUploadedFiles
     * @covers Fracture\Http\Request::getUpload
     */
    public function testAdditionOfUploadsWithoutBuilder()
    {
        $input = [
            'alpha' => [
                'name'      => 'simple.png',
                'type'      => 'image/png',
                'tmp_name'  => FIXTURE_PATH . '/files/simple.png',
                'error'     => 0,
                'size'      => 74,
            ],
        ];

        $instance = new Request;
        $instance->setUploadedFiles($input);

        $this->assertEquals($input[ 'alpha' ], $instance->getUpload('alpha'));
    }


    /**
     * @covers Fracture\Http\Request::__construct
     * @covers Fracture\Http\Request::setUploadedFiles
     */
    public function testCallOnFileBagBuilderWhenSettingUploads()
    {
        $input = [
            'alpha' => [
                'name'      => 'simple.png',
                'type'      => 'image/png',
                'tmp_name'  => FIXTURE_PATH . '/files/simple.png',
                'error'     => 0,
                'size'      => 74,
            ],
        ];

        $builder = $this->getMock(
            'Fracture\Http\FileBagBuilder',
            ['create'],
            [
                'uploadedFileBuilder' => $this->getMock('Fracture\Http\UploadedFileBuilder')
            ]
        );
        $builder->expects($this->once())
                ->method('create')
                ->with($this->equalTo($input));

        $instance = new Request($builder);
        $instance->setUploadedFiles($input);
    }


    /**
     * @covers Fracture\Http\Request::__construct
     * @covers Fracture\Http\Request::setAcceptHeader
     * @covers Fracture\Http\Request::getAcceptHeader
     */
    public function testGetterAndSetterForAcceptHeader()
    {
        $request = new Request;
        $header = $this->getMock('Fracture\Http\Headers\Accept');
        $request->setAcceptHeader($header);
        $this->assertEquals($header, $request->getAcceptHeader());
    }



    /**
     * @covers Fracture\Http\Request::__construct
     * @covers Fracture\Http\Request::setContentTypeHeader
     * @covers Fracture\Http\Request::getContentTypeHeader
     */
    public function testGetterAndSetterForContentTypeHeader()
    {
        $request = new Request;
        $header = $this->getMock('Fracture\Http\Headers\ContentType');
        $request->setContentTypeHeader($header);
        $this->assertEquals($header, $request->getContentTypeHeader());
    }


    /**
     * @covers Fracture\Http\Request::__construct
     * @covers Fracture\Http\Request::getCookie
     * @covers Fracture\Http\Request::addCookie
     */
    public function testAddedCookie()
    {
        $request = new Request;
        $this->assertNull($request->getCookie('alpha'));

        $request->addCookie('alpha', 'value');
        $this->assertEquals('value', $request->getCookie('alpha'));
    }
}
