<?php


namespace Fracture\Http;

use PHPUnit_Framework_TestCase;

class RequestBuilderTest extends PHPUnit_Framework_TestCase
{



    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::buildInstance
     */
    public function testSimpleUsecase()
    {
        $builder = new RequestBuilder;
        $instance = $builder->create([]);
        $this->assertInstanceOf('Fracture\Http\Request', $instance);
    }

    /**
     * @covers Fracture\Http\RequestBuilder::create
     */
    public function testInternalManipulationsofInstance()
    {
        $request = new Request(
            new FileBagBuilder(
                new UploadedFileBuilder
            )
        );

        $params = [
            'get'    => [],
            'post'   => [],
            'server' => [],
            'files'  => [],
            'cookies'=> [],
        ];


        $builder = $this->getMock('Fracture\Http\RequestBuilder', ['buildInstance', 'applyParams']);

        $builder->expects($this->once())
                ->method('buildInstance')
                ->will($this->returnValue($request));

        $builder->expects($this->once())
                ->method('applyParams')
                ->with($this->equalTo($request), $this->equalTo($params));


        $instance = $builder->create([]);
        $this->assertInstanceOf('Fracture\Http\Request', $instance);
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyParams
     * @covers Fracture\Http\RequestBuilder::applyWebContext
     */
    public function testMethodCallsOnInstance()
    {
        $request = $this->getMock(
            'Fracture\Http\Request',
            [
                'setParameters',
                'setMethod',
                'setUploadedFiles',
                'setAddress',
            ]
        );

        $request->expects($this->exactly(2))->method('setParameters');
        $request->expects($this->once())->method('setMethod');

        $builder = $this->getMock('Fracture\Http\RequestBuilder', ['buildInstance']);

        $builder->expects($this->once())
                ->method('buildInstance')
                ->will($this->returnValue($request));


        $request->expects($this->once())->method('setUploadedFiles');
        $request->expects($this->once())->method('setAddress');

        $instance = $builder->create([
            'get'    => [],
            'post'   => [],
            'server' => [
                'REQUEST_METHOD' => 'post',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
            ],
            'files'  => [],
        ]);
        $this->assertInstanceOf('Fracture\Http\Request', $instance);
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyParams
     */
    public function testMethodCallsOnInstanceForCLI()
    {
        $builder = new RequestBuilder;


        $instance = $builder->create([
            'get'    => [],
            'post'   => [],
            'files'  => [],
        ]);
        $this->assertInstanceOf('Fracture\Http\Request', $instance);
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::buildInstance
     */
    public function testUnalteredInstance()
    {
        $input = [
            'get'  => [],
            'server' => [
                'REQUEST_METHOD' => 'post',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
            ],
        ];

        $params = [
            'get'    => [],
            'post'   => [],
            'server' => [
                'REQUEST_METHOD' => 'post',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
            ],
            'files'  => [],
            'cookies'=> [],
        ];



        $builder = $this->getMock('Fracture\Http\RequestBuilder', ['applyParams']);

        $builder->expects($this->once())
                ->method('applyParams')
                ->with($this->isInstanceOf('\Fracture\Http\Request'), $this->equalTo($params));

        $builder->create($input);
    }

    /**
     * @covers Fracture\Http\RequestBuilder::create
     */
    public function testWhenContentParsersApplied()
    {
        $input = [
            'get'    => [],
            'server' => [
                'REQUEST_METHOD' => 'put',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
            ],
        ];

        $builder = $this->getMock('Fracture\Http\RequestBuilder', ['applyContentParsers']);

        $builder->expects($this->once())
                ->method('applyContentParsers')
                ->with($this->isInstanceOf('\Fracture\Http\Request'));

        $builder->create($input);
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     */
    public function testWhenContentParsersIgnored()
    {
        $input = [
            'get'    => [],
            'server' => [
                'REQUEST_METHOD' => 'get',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
            ],
        ];

        $builder = $this->getMock('Fracture\Http\RequestBuilder', ['applyContentParsers']);

        $builder->expects($this->never())
                ->method('applyContentParsers');

        $builder->create($input);
    }



    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     *
     * @covers Fracture\Http\RequestBuilder::alterParameters
     */
    public function testAppliedContentParsers()
    {
        $input = [
            'get'    => [],
            'server' => [
                'REQUEST_METHOD' => 'delete',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
                'CONTENT_TYPE'   => 'application/json',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('application/json', function () {
            return ['foo' => 'bar'];
        });

        $instance = $builder->create($input);
        $this->assertEquals('bar', $instance->getParameter('foo'));

    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     *
     * @covers Fracture\Http\RequestBuilder::alterParameters
     */
    public function testAppliedContentParsersOverridesPameters()
    {
        $input = [
            'get'    => [
                'foo' => 'bar',
            ],
            'server' => [
                'REQUEST_METHOD' => 'delete',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
                'CONTENT_TYPE'   => 'application/json',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('application/json', function () {
            return ['foo' => 'different'];
        });

        $instance = $builder->create($input);
        $this->assertEquals('different', $instance->getParameter('foo'));

    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     *
     * @covers Fracture\Http\RequestBuilder::alterParameters
     *
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testAppliedContentParsersWithBadReturn()
    {
        $input = [
            'get'    => [
                'foo' => 'bar',
            ],
            'server' => [
                'REQUEST_METHOD' => 'delete',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
                'CONTENT_TYPE'   => 'application/json',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('application/json', function () {
            return null;
        });

        $instance = $builder->create($input);
        $this->assertEquals('bar', $instance->getParameter('foo'));

    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     */
    public function testAppliedContentParsersWithMissingHeader()
    {
        $input = [
            'get'    => [],
            'server' => [
                'REQUEST_METHOD' => 'delete',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('application/json', function () {
            return ['foo' => 'bar'];
        });

        $instance = $builder->create($input);
        $this->assertEquals(null, $instance->getParameter('foo'));

    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     */
    public function testAppliedContentParsersWithHeader()
    {
        $input = [
            'server' => [
                'CONTENT_TYPE'    => 'text/html;version=2',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('text/html', function ($header) {
            return ['foo' => $header->getParameter('version')];
        });

        $instance = $builder->create($input);
        $this->assertEquals(2, $instance->getParameter('foo'));
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     */
    public function testAppliedContentParsersWithRequest()
    {
        $input = [
            'get'    => [
                'test' => 'value',
            ],
            'server' => [
                'CONTENT_TYPE'    => 'text/html',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('text/html', function ($header, $request) {
            return ['duplicate' => $request->getParameter('test')];
        });

        $instance = $builder->create($input);
        $this->assertEquals('value', $instance->getParameter('duplicate'));
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     */
    public function testOverrideRequestMethodWithParser()
    {
        $input = [
            'get'    => [
                '_mark' => 'put',
            ],
            'server' => [
                'CONTENT_TYPE'    => 'text/html',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('text/html', function ($header, $request) {
            $method = $request->getParameter('_mark');
            $request->setMethod($method);
            return [];
        });

        $instance = $builder->create($input);
        $this->assertEquals('put', $instance->getMethod());
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::addContentParser
     */
    public function testAppliedParserForWildecard()
    {
        $input = [
            'server' => [
                'CONTENT_TYPE'    => 'text/html',
            ],
        ];

        $builder = new RequestBuilder;
        $builder->addContentParser('*/*', function ($header, $request) {
            return ['called' => true];
        });

        $instance = $builder->create($input);
        $this->assertTrue($instance->getParameter('called'));
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::applyHeaders
     */
    public function testIfAcceptHeaderApplied()
    {
        $input = [
            'get'    => [],
            'server' => [
                'REQUEST_METHOD' => 'post',
                'REMOTE_ADDR'    => '0.0.0.0',
                'HTTP_ACCEPT'    => 'text/html',
            ],
        ];

        $builder = new RequestBuilder;
        $instance = $builder->create($input);

        $this->assertInstanceOf('Fracture\Http\Headers\Accept', $instance->getAcceptHeader());
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyContentParsers
     * @covers Fracture\Http\RequestBuilder::applyHeaders
     */
    public function testIfContentTypeHeaderApplied()
    {
        $input = [
            'get'    => [],
            'server' => [
                'REQUEST_METHOD' => 'post',
                'REMOTE_ADDR'    => '0.0.0.0',
                'CONTENT_TYPE'   => 'application/json',
            ],
        ];


        $builder = new RequestBuilder;
        $instance = $builder->create($input);

        $this->assertInstanceOf('Fracture\Http\Headers\ContentType', $instance->getContentTypeHeader());
    }


    /**
     * @covers Fracture\Http\RequestBuilder::create
     * @covers Fracture\Http\RequestBuilder::applyParams
     *
     * @covers Fracture\Http\RequestBuilder::alterParameters
     */
    public function testWithCookieAddition()
    {
        $input = [
            'cookies' => [
                'name' => 'value'
            ]
        ];

        $builder = new RequestBuilder;
        $instance = $builder->create($input);

        $this->assertEquals('value', $instance->getCookie('name'));
    }
}
