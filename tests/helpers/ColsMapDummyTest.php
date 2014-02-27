<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\tests\helpers;

use axy\trimdb\helpers\ColsMapDummy;

/**
 * @coversDefaultClass use axy\trimdb\helpers\ColsMapDummy
 */
class ColsMapDummyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::fromField
     */
    public function testFromField()
    {
        $cols = new ColsMapDummy();
        $this->assertSame('one', $cols->fromField('one'));
        $this->assertSame('three', $cols->fromField('three'));
    }

    /**
     * @covers ::toField
     */
    public function testToField()
    {
        $cols = new ColsMapDummy($this->cols);
        $this->assertSame('x', $cols->toField('x'));
        $this->assertSame('z', $cols->toField('z'));
    }

    /**
     * @covers ::fromList
     */
    public function testFromList()
    {
        $cols = new ColsMapDummy();
        $this->assertSame(['one', 'three', 'two'], $cols->fromList(['one', 'three', 'two']));
    }

    /**
     * @covers ::fromDict
     */
    public function testFromDict()
    {
        $cols = new ColsMapDummy();
        $dict = [
            'one' => 5,
            'y' => 6,
            'z' => 7,
        ];
        $expected = $dict;
        $this->assertSame($expected, $cols->fromDict($dict));
    }

    /**
     * @covers ::fromMultiDict
     */
    public function testFromMultiDict()
    {
        $cols = new ColsMapDummy();
        $mdict = [
            'qwe' => [
                'one' => 1,
                'two' => 'qwe',
            ],
            'rty' => [
                'two' => 'rty',
                'three' => '55',
            ],
        ];
        $expected = $mdict;
        $this->assertSame($expected, $cols->fromMultiDict($mdict));
    }

    /**
     * @covers ::fromToDict
     */
    public function testToDict()
    {
        $cols = new ColsMapDummy();
        $rcols = [
            'one' => [
                'x' => 1,
                'y' => 10,
                'z' => 20,
            ],
            'two' => [
                'y' => 10,
                'z' => 20,
            ],
        ];
        $expected = $rcols;
        $this->assertSame($expected, $cols->toDict($rcols));
    }
}
