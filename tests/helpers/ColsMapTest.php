<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\tests\helpers;

use axy\trimdb\helpers\ColsMap;

/**
 * @coversDefaultClass use axy\trimdb\helpers\ColsMap
 */
class ColsMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $cols = [
        'one' => 'x',
        'two' => 'y',
    ];

    /**
     * @covers ::fromField
     */
    public function testFromField()
    {
        $cols = new ColsMap($this->cols);
        $this->assertSame('x', $cols->fromField('one'));
        $this->assertSame('three', $cols->fromField('three'));
    }

    /**
     * @covers ::toField
     */
    public function testToField()
    {
        $cols = new ColsMap($this->cols);
        $this->assertSame('one', $cols->toField('x'));
        $this->assertSame('z', $cols->toField('z'));
    }

    /**
     * @covers ::fromList
     */
    public function testFromList()
    {
        $cols = new ColsMap($this->cols);
        $this->assertSame(['x', 'three', 'y'], $cols->fromList(['one', 'three', 'two']));
    }

    /**
     * @covers ::fromDict
     */
    public function testFromDict()
    {
        $cols = new ColsMap($this->cols);
        $dict = [
            'one' => 5,
            'y' => 6,
            'z' => 7,
        ];
        $expected = [
            'x' => 5,
            'y' => 6,
            'z' => 7,
        ];
        $this->assertSame($expected, $cols->fromDict($dict));
    }

    /**
     * @covers ::fromMultiDict
     */
    public function testFromMultiDict()
    {
        $cols = new ColsMap($this->cols);
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
        $expected = [
            'qwe' => [
                'x' => 1,
                'y' => 'qwe',
            ],
            'rty' => [
                'y' => 'rty',
                'three' => '55',
            ],
        ];
        $this->assertSame($expected, $cols->fromMultiDict($mdict));
    }

    /**
     * @covers ::fromToDict
     */
    public function testToDict()
    {
        $cols = new ColsMap($this->cols);
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
        $expected = [
            'one' => [
                'one' => 1,
                'two' => 10,
                'z' => 20,
            ],
            'two' => [
                'two' => 10,
                'z' => 20,
            ],
        ];
        $this->assertSame($expected, $cols->toDict($rcols));
    }
}
