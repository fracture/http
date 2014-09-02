<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class AcceptTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::getPrioritizedList
     *
     * @covers Fracture\Http\Headers\Accept::obtainGroupedElements
     * @covers Fracture\Http\Headers\Accept::obtainSortedQualityList
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     * @covers Fracture\Http\Headers\Accept::obtainSortedElements
     */
    public function testEmptyInstance()
    {
        $instance = new Accept;
        $instance->prepare();

        $this->assertEquals([], $instance->getPrioritizedList());
    }


    /**
     * @dataProvider provideSimpleAccepts
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::getPrioritizedList
     *
     * @covers Fracture\Http\Headers\Accept::obtainGroupedElements
     * @covers Fracture\Http\Headers\Accept::obtainSortedQualityList
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     * @covers Fracture\Http\Headers\Accept::obtainSortedElements
     */
    public function testSimpleAccepts($input, $expected)
    {
        $instance = new Accept($input);
        $instance->prepare();

        $this->assertEquals($expected, $instance->getPrioritizedList());
    }


    public function provideSimpleAccepts()
    {
        return [
            [
                'input' => 'text/html',
                'expected' => [['value' => 'text/html']],
            ],
            [
                'input' => 'text/html;version=2',
                'expected' => [['value' => 'text/html', 'version' => '2']],
            ],
            [
                'input' => 'text/html;foo=bar; q=0.6',
                'expected' => [['value' => 'text/html', 'foo' => 'bar']],
            ],
            [
                'input' => 'application/json;version=1, application/json, */*;q=0.5',
                'expected' => [
                    ['value' => 'application/json', 'version' => '1'],
                    ['value' => 'application/json'], ['value' => '*/*']
                ],
            ],
            [
                'input' => 'application/json;version=1, test/test;q=0.8, application/json, */*;q=0.5',
                'expected' => [
                    ['value' => 'application/json', 'version' => '1'],
                    ['value' => 'application/json'],
                    ['value' => 'test/test'],
                    ['value' => '*/*']
                ],
            ],
        ];

    }


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::setValue
     * @covers Fracture\Http\Headers\Accept::getPrioritizedList
     *
     * @covers Fracture\Http\Headers\Accept::obtainGroupedElements
     * @covers Fracture\Http\Headers\Accept::obtainSortedQualityList
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     * @covers Fracture\Http\Headers\Accept::obtainSortedElements
     */
    public function testUseOfAlternativeValue()
    {
        $instance = new Accept('text/plain');
        $instance->prepare();

        $this->assertEquals([['value' => 'text/plain']], $instance->getPrioritizedList());

        $instance->setValue('text/html');
        $instance->prepare();

        $this->assertEquals([['value' => 'text/html']], $instance->getPrioritizedList());
    }

    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::contains
     *
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     */
    public function testWhetherContainFindsExistingType()
    {
        $instance = new Accept('application/json;version=1;param=value, application/json');
        $instance->prepare();

        $this->assertTrue($instance->contains('application/json;param=value;version=1'));
        $this->assertFalse($instance->contains('application/json;version=value;param=1'));
    }



    /**
     * @dataProvider provideTypesForComputation
     * @covers Fracture\Http\Headers\Accept::getPreferred
     * @covers Fracture\Http\Headers\Accept::extractData
     * @covers Fracture\Http\Headers\Accept::obtainEntryFromList
     * @covers Fracture\Http\Headers\Accept::isMatch
     * @covers Fracture\Http\Headers\Accept::replaceStars
     *
     */
    public function testPreferredTypeCompution($header, $available, $expected)
    {
        $instance = new Accept($header);
        $instance->prepare();

        $this->assertEquals($expected, $instance->getPreferred($available));
    }

    public function provideTypesForComputation()
    {
        return [
            [
                'header'    => 'application/json',
                'available' => 'application/json',
                'expected'  => 'application/json',
            ],
            [
                'header'    => '*/*',
                'available' => 'application/json',
                'expected'  => 'application/json',
            ],
            [
                'header'    => 'application/json;version=2',
                'available' => 'application/json;version=1',
                'expected'  => null,
            ],
            [
                'header'    => 'application/json',
                'available' => 'text/html, application/json',
                'expected'  => 'application/json',
            ],
            [
                'header'    => 'text/html;q=0.1, application/json',
                'available' => 'application/json',
                'expected'  => 'application/json',
            ],
            [
                'header'    => 'text/html, application/json',
                'available' => 'application/json',
                'expected'  => 'application/json',
            ],
            [
                'header'    => 'text/html;q=0.1, application/json;q=0.4',
                'available' => 'application/json',
                'expected'  => 'application/json',
            ],
            [
                'header'    => 'text/html, application/json, text/*',
                'available' => 'text/plain',
                'expected'  => 'text/plain',
            ],
            [
                'header'    => 'text/html, application/json',
                'available' => 'application/json, text/html',
                'expected'  => 'text/html',
            ],
            [
                'header'    => 'application/json',
                'available' => 'application/json;version=2',
                'expected'  => null,
            ],
            [
                'header'    => 'application/json;version=3, application/json',
                'available' => 'application/json;version=2, application/json',
                'expected'  => 'application/json',
            ],
            [
                'header'    => 'application/json;version=3, application/json',
                'available' => 'application/json;version=2, application/json;version=3',
                'expected'  => 'application/json;version=3',
            ],
        ];
    }


    /**
     * @dataProvider provideEntriesForFormating
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::getFormatedEntry
     */
    public function testFormatingofEntries($entry, $result)
    {
        $instance = new Accept;
        $this->assertEquals($result, $instance->getFormatedEntry($entry));
    }


    public function provideEntriesForFormating()
    {
        return [
            [
                'entry' => ['value' => 'text/html'],
                'result' => 'text/html',
            ],
            [
                'entry' => ['value' => 'text/html', 'version' => '2'],
                'result' => 'text/html;version=2',
            ],
        ];
    }


    /*
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::getName
     */
    public function testGivenName()
    {
        $instance = new Accept;
        $this->assertSame('Accept', $instance->getName());
    }
}
