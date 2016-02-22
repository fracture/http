<?php


namespace Fracture\Http\Headers;

use Exception;
use ReflectionClass;
use PHPUnit_Framework_TestCase;

class AcceptTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::getParsedData
     */
    public function testEmptyInstance()
    {
        $instance = new Accept;
        $instance->prepare();

        $this->assertEquals(null, $instance->getParsedData());
    }


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::getParsedData
     * @covers Fracture\Http\Headers\Accept::extractData
     *
     * @covers Fracture\Http\Headers\Accept::obtainGroupedElements
     * @covers Fracture\Http\Headers\Accept::obtainSortedQualityList
     * @covers Fracture\Http\Headers\Accept::obtainSortedElements
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     *
     * @dataProvider provideSimpleAccepts
     */
    public function testSimpleAccepts($input, $expected)
    {
        $instance = new Accept($input);
        $instance->prepare();

        $this->assertEquals($expected, $instance->getParsedData());
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
     * @covers Fracture\Http\Headers\Accept::getParsedData
     * @covers Fracture\Http\Headers\Accept::extractData
     *
     * @covers Fracture\Http\Headers\Accept::obtainGroupedElements
     * @covers Fracture\Http\Headers\Accept::obtainSortedQualityList
     * @covers Fracture\Http\Headers\Accept::obtainSortedElements
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     *
     * @dataProvider provideAcceptPrecedence
     */
    public function testAcceptPrecedence($input, $expected)
    {
        $instance = new Accept($input);
        $instance->prepare();

        $this->assertEquals($expected, $instance->getParsedData());
    }


    public function provideAcceptPrecedence()
    {
        return [
            [
                'input' => 'type/subtype, type/subtype;param=1',
                'expected' => [
                    ['value' => 'type/subtype', 'param' => '1'],
                    ['value' => 'type/subtype'],
                ],
            ],
            [
                'input' => 'type/subtype;param=1, type/subtype',
                'expected' => [
                    ['value' => 'type/subtype', 'param' => '1'],
                    ['value' => 'type/subtype'],
                ],
            ],
            [
                'input' => 'text/*, text/html, text/html;level=1, */*',
                'expected' => [
                    ['value' => 'text/html', 'level' => '1'],
                    ['value' => 'text/html'],
                    ['value' => 'text/*'],
                    ['value' => '*/*'],
                ],
            ],
            [
                'input' => 'application/*, application/json;type=1, application/json; level=1; type=2, */*',
                'expected' => [
                    ['value' => 'application/json', 'level' => '1', 'type' => '2'],
                    ['value' => 'application/json', 'type' => '1'],
                    ['value' => 'application/*'],
                    ['value' => '*/*'],
                ],
            ],
        ];
    }


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::getParsedData
     * @covers Fracture\Http\Headers\Accept::extractData
     *
     * @covers Fracture\Http\Headers\Accept::obtainGroupedElements
     * @covers Fracture\Http\Headers\Accept::obtainSortedQualityList
     * @covers Fracture\Http\Headers\Accept::obtainSortedElements
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     */
    public function testUseOfAlternativeValue()
    {
        $instance = new Accept('text/plain');
        $instance->prepare();

        $this->assertEquals([['value' => 'text/plain']], $instance->getParsedData());

        $instance->setValue('text/html');
        $instance->prepare();

        $this->assertEquals([['value' => 'text/html']], $instance->getParsedData());
    }


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::contains
     *
     * @covers Fracture\Http\Headers\Accept::obtainAssessedItem
     * @covers Fracture\Http\Headers\Accept::matchFound
     * @covers Fracture\Http\Headers\Accept::isMatch
     * @covers Fracture\Http\Headers\Accept::replaceStars
     */
    public function testWhetherContainFindsExistingType()
    {
        $instance = new Accept('application/json;version=1;param=value, application/json');
        $instance->prepare();

        $this->assertTrue($instance->contains('application/json;param=value;version=1'));
        $this->assertFalse($instance->contains('application/json;version=value;param=1'));
    }


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::contains
     */
    public function testContainsForEmptyValue()
    {
        $instance = new Accept('');
        $instance->prepare();

        $this->assertFalse($instance->contains('application/json'));
    }


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::getPreferred
     *
     * @covers Fracture\Http\Headers\Accept::findFormatedEntry
     * @covers Fracture\Http\Headers\Accept::obtainEntryFromList
     * @covers Fracture\Http\Headers\Accept::getFormatedEntry
     * @covers Fracture\Http\Headers\Accept::replaceStars
     * @covers Fracture\Http\Headers\Accept::sortBySpecificity
     * @covers Fracture\Http\Headers\Accept::computeSpecificity
     *
     * @dataProvider provideTypesForComputation
     */
    public function testPreferredTypeCompution($header, $available, $expected)
    {
        $instance = new Accept($header);
        $instance->prepare();

        $this->assertEquals($expected, $instance->getPreferred($available));
    }


    public function provideTypesForComputation()
    {
        return include FIXTURE_PATH . '/headers/accept-preferred.php';
    }


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::prepare
     * @covers Fracture\Http\Headers\Accept::getPreferred
     *
     * @covers Fracture\Http\Headers\Accept::sortBySpecificity
     * @covers Fracture\Http\Headers\Accept::computeSpecificity
     */
    public function testPreferredTypeComputionForEmptyHeaderValue()
    {
        $instance = new Accept('');
        $instance->prepare();

        $this->assertNull($instance->getPreferred('application/json;version=2, application/json;version=3'));
    }

    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::getFormatedEntry
     *
     * @dataProvider provideEntriesForFormating
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


    /**
     * @covers Fracture\Http\Headers\Accept::__construct
     * @covers Fracture\Http\Headers\Accept::getName
     */
    public function testGivenName()
    {
        $instance = new Accept;
        $this->assertSame('Accept', $instance->getName());
    }
}
