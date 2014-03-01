<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\tests\storages;

use axy\trimdb\storages\EmptyStorage;

/**
 * @coversDefaultClass axy\trimdb\storages\EmptyStorage
 */
class EmptyStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var axy\trimdb\storages\EmptyStorage
     */
    private $storage;

    /**
     * @var array
     */
    private $data = [
        1 => ['id' => 1, 'a' => 2],
        2 => ['id' => 2, 'a' => 4],
        3 => ['id' => 3, 'a' => 6],
    ];

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $config = [
            'data' => $this->data,
            'param' => 'qwerty',
        ];
        $this->storage = new EmptyStorage($config);
    }

    /**
     * @covers ::__construct
     * @covers ::getData
     * @covers ::getConfig
     */
    public function testCreate()
    {
        $this->assertEquals($this->data, $this->storage->getData());
        $expected = [
            'data' => $this->data,
            'param' => 'qwerty',
        ];
        $this->assertEquals($expected, $this->storage->getConfig());
    }

    /**
     * @covers ::selectById
     */
    public function testSelectById()
    {
        $expected = ['id' => 3, 'a' => 6];
        $this->assertEquals($expected, $this->storage->selectById(3));
        $expected = ['a' => 4];
        $this->assertEquals($expected, $this->storage->selectById(2, ['a']));
        $this->assertNull($this->storage->selectById(10));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->selectById(2, ['unk']);
    }

    /**
     * @covers ::selectByListIds
     */
    public function testSelectByListIds()
    {
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            5 => null,
            3 => ['id' => 3, 'a' => 6],
        ];
        $this->assertEquals($expected, $this->storage->selectByListIds([1, 5, 3]));
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            3 => ['id' => 3, 'a' => 6],
        ];
        $this->assertEquals($expected, $this->storage->selectByListIds([1, 5, 3], 'id', true));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->selectByListIds([5, 3], ['title', 'unk']);
    }

    /**
     * @covers ::selectByField
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testSelectByField()
    {
        $this->storage->selectByField('a', 1, ['b'], 'unk');
    }

    /**
     * @covers ::selectByWhere
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testSelectByWhere()
    {
        $this->storage->selectByWhere(['b' => [6, 7, 8, 10]], ['id', 'b'], ['id' => false]);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateById()
    {
        $this->assertTrue($this->storage->updateById(2, ['a' => 3]));
        $this->assertFalse($this->storage->updateById(2, ['a' => 3]));
        $this->assertFalse($this->storage->updateById(5, ['a' => 3]));
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 3],
            3 => ['id' => 3, 'a' => 6],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::updateByListIds
     */
    public function testUpdateByListIds()
    {
        $this->assertSame(1, $this->storage->updateByListIds([1, 2, 7], ['a' => 2]));
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 2],
            3 => ['id' => 3, 'a' => 6],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::updateByListIds
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testUpdateByField()
    {
        $this->storage->updateByField('zz', 44, [ 'b' => 25]);
    }

    /**
     * @covers ::updateByWhere
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testUpdateByWhere()
    {
        $this->storage->updateByWhere(['unk' => 1], ['title' => 'x']);
    }

    /**
     * @covers ::deleteById
     */
    public function testDeleteById()
    {
        $this->assertTrue($this->storage->deleteById(3));
        $this->assertFalse($this->storage->deleteById(33));
        $this->assertFalse($this->storage->deleteById(3));
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 4],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::deleteByListIds
     */
    public function testDeleteByListIds()
    {
        $this->assertSame(2, $this->storage->deleteByListIds([1, 3, 5]));
        $expected = [
            2 => ['id' => 2, 'a' => 4],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::deleteByField
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testDeleteByField()
    {
        $this->storage->deleteByField('unk', 1);
    }

    /**
     * @covers ::deleteByWhere
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testDeleteByWhere()
    {
        $this->storage->deleteByWhere(['bbb' => [8, 7, 4]]);
    }

    /**
     * @covers ::truncate
     */
    public function testTruncate()
    {
        $this->storage->truncate();
        $this->assertEmpty($this->storage->getData());
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $set = [
            'a' => 27,
        ];
        $this->assertSame(4, $this->storage->insert($set));
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 4],
            3 => ['id' => 3, 'a' => 6],
            4 => ['id' => 4, 'a' => 27],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::multiInsert
     */
    public function testMultiInsert()
    {
        $sets = [
            [
                'a' => 14,
            ],
            [
                'a' => 27,
            ],
        ];
        $this->storage->multiInsert($sets);
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 4],
            3 => ['id' => 3, 'a' => 6],
            4 => ['id' => 4, 'a' => 14],
            5 => ['id' => 5, 'a' => 27],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::replaceById
     */
    public function testReplaceById()
    {
        $this->storage->replaceById(3, ['a' => 123]);
        $this->storage->replaceById(55, ['a' => 456]);
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 4],
            3 => ['id' => 3, 'a' => 123],
            55 => ['id' => 55, 'a' => 456],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::multiReplaceById
     */
    public function testMultiReplaceById()
    {
        $sets = [
            3 => ['a' => 123],
            55 => ['a' => 456],
        ];
        $this->storage->multiReplaceById($sets);
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 4],
            3 => ['id' => 3, 'a' => 123],
            55 => ['id' => 55, 'a' => 456],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::replace
     */
    public function testReplace()
    {
        $this->storage->replace(['id' => 3, 'a' => 123]);
        $this->storage->replace(['id' => 55, 'a' => 456]);
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 4],
            3 => ['id' => 3, 'a' => 123],
            55 => ['id' => 55, 'a' => 456],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::multiReplace
     */
    public function testMultiReplace()
    {
        $sets = [
            ['id' => 3, 'a' => 123],
            ['id' => 55, 'a' => 456],
        ];
        $this->storage->multiReplace($sets);
        $expected = [
            1 => ['id' => 1, 'a' => 2],
            2 => ['id' => 2, 'a' => 4],
            3 => ['id' => 3, 'a' => 123],
            55 => ['id' => 55, 'a' => 456],
        ];
        $this->assertEquals($expected, $this->storage->getData());
    }

    /**
     * @covers ::countAll
     */
    public function testCountAll()
    {
        $this->assertEquals(3, $this->storage->countAll());
    }

    /**
     * @covers ::countByField
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testCountByField()
    {
        $this->storage->countByField('a', 1);
    }

    /**
     * @covers ::countByWhere
     * @expectedException axy\trimdb\errors\NotSupported
     */
    public function testCountByWhere()
    {
        $this->storage->countByWhere(['zz' => 25]);
    }

    /**
     * @covers ::existsId
     */
    public function testExistsId()
    {
        $this->assertTrue($this->storage->existsId(2));
        $this->assertFalse($this->storage->existsId(22));
    }
}
