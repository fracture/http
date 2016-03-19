<?php


namespace Fracture\Http;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class FileCatalogTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\FileCatalog::offsetExists
     * @covers Fracture\Http\FileCatalog::offsetSet
     * @covers Fracture\Http\FileCatalog::offsetGet
     * @covers Fracture\Http\FileCatalog::offsetUnset
     */
    public function testArrayAccessForOneValidItem()
    {
        $instance = new FileCatalog;


        $item  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $item->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(true));

        $this->assertFalse(isset($instance['foo']));
        $instance['foo'] = $item;
        $this->assertTrue(isset($instance['foo']));
        $this->assertEquals($item, $instance['foo']);
        unset($instance['foo']);
        $this->assertFalse(isset($instance['foo']));
    }


    /**
     * @covers Fracture\Http\FileCatalog::offsetExists
     * @covers Fracture\Http\FileCatalog::offsetSet
     */
    public function testArrayAccessForOneValidItemwithoutKey()
    {
        $instance = new FileCatalog;


        $item  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $item->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(true));

        $this->assertFalse(isset($instance[0]));
        $instance[] = $item;
        $this->assertTrue(isset($instance[0]));
    }

    /**
     * @covers Fracture\Http\FileCatalog::offsetExists
     * @covers Fracture\Http\FileCatalog::offsetSet
     * @covers Fracture\Http\FileCatalog::offsetGet
     * @covers Fracture\Http\FileCatalog::offsetUnset
     */
    public function testArrayAccessForOneInalidItem()
    {
        $instance = new FileCatalog;


        $item  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $item->expects($this->once())
             ->method('isValid')
             ->will($this->returnValue(false));

        $this->assertFalse(isset($instance['foo']));
        $instance['foo'] = $item;
        $this->assertFalse(isset($instance['foo']));
        $this->assertNull($instance['foo']);
        unset($instance['foo']);
        $this->assertFalse(isset($instance['foo']));
    }


    /**
     * @dataProvider provideListOfUploadedFileInstances
     *
     * @covers Fracture\Http\FileCatalog::current
     * @covers Fracture\Http\FileCatalog::key
     * @covers Fracture\Http\FileCatalog::next
     * @covers Fracture\Http\FileCatalog::rewind
     * @covers Fracture\Http\FileCatalog::valid
     */
    public function testIteratorWithForeachLoop($first, $second, $third)
    {
        $instance = new FileCatalog;

        $instance['a'] = $first;
        $instance['b'] = $second;
        $instance['c'] = $third;


        $expected = ['a' =>  $first, 'b' => $second, 'c' => $third];
        $keys = ['a', 'b', 'c'];

        foreach ($instance as $key => $value) {
            $this->assertEquals(array_shift($keys), $key);
            $this->assertEquals($expected[$key], $value);
        }
    }


    /**
     * @dataProvider provideListOfUploadedFileInstances
     *
     * @covers Fracture\Http\FileCatalog::current
     * @covers Fracture\Http\FileCatalog::key
     * @covers Fracture\Http\FileCatalog::next
     * @covers Fracture\Http\FileCatalog::rewind
     * @covers Fracture\Http\FileCatalog::valid
     */
    public function testIteratorDirectlyWithNumericKeys($first, $second, $third)
    {
        $instance = new FileCatalog;

        $instance[] = $first;
        $instance[] = $second;
        $instance[] = $third;

        $this->assertEquals($first, $instance->current());
        $instance->next();
        $this->assertEquals(1, $instance->key());
        $instance->next();
        $instance->next();
        $this->assertFalse($instance->valid());
        $this->assertEquals(3, $instance->key());
        $instance->rewind();
        $this->assertEquals(0, $instance->key());
        $instance->next();
        $this->assertEquals($second, $instance->current());

    }


    public function provideListOfUploadedFileInstances()
    {
        $first  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $first->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(true));

        $second  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $second->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(true));

        $third  = $this->getMock('Fracture\Http\UploadedFile', ['isValid'], ['foo' => 'bar']);
        $third->expects($this->once())
                 ->method('isValid')
                 ->will($this->returnValue(true));

        return [[
            'first'  => $first,
            'second' => $second,
            'third'  => $third,
        ]];
    }
}
