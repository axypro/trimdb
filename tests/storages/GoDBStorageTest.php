<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\tests\storages;

use axy\trimdb\storages\GoDBStorage;
use go\DB\DB;
use axy\trimdb\tests\dbtest\Factory as TestFactory;

/**
 * @coversDefaultClass axy\trimdb\storages\GoDBStorage
 */
class GoDBStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var axy\trimdb\storages\GoDBStorage
     */
    private $storage;

    /**
     * @var \go\DB\DB
     */
    private $db;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->db = TestFactory::getDB(__DIR__.'/storage.sql');
        if (!$this->db) {
            $this->markTestSkipped('required mysql params');
        }
        $map = [
            'id' => 'id_z',
            'title' => 'title_z',
            'b' => 'b_z',
        ];
        $config = [
            'table' => $this->db->getTable('test', $map),
        ];
        $this->storage = new GoDBStorage($config);
    }

    /**
     * @covers ::selectById
     */
    public function testSelectById()
    {
        $expected = [
            'id' => 3,
            'title' => 'three',
            'a' => 1,
            'b' => 6,
        ];
        $this->assertEquals($expected, $this->storage->selectById(3));
        $expected = [
            'a' => 2,
            'b' => 4,
        ];
        $this->assertEquals($expected, $this->storage->selectById(4, ['a', 'b']));
        $this->assertNull($this->storage->selectById(10));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->selectById(4, ['unk']);
    }

    /**
     * @covers ::selectByListIds
     */
    public function testSelectByListIds()
    {
        $expected = [
            5 => ['id' => 5, 'title' => 'five'],
            10 => null,
            3 => ['id' => 3, 'title' => 'three'],
        ];
        $this->assertEquals($expected, $this->storage->selectByListIds([5, 10, 3], ['id', 'title']));
        $expected = [
            5 => ['id' => 5, 'title' => 'five'],
            3 => ['id' => 3, 'title' => 'three'],
            10 => null,
        ];
        $this->assertEquals($expected, $this->storage->selectByListIds([5, 3, 10], ['id', 'title']));
        $expected = [
            5 => ['id' => 5, 'title' => 'five'],
            3 => ['id' => 3, 'title' => 'three'],
        ];
        $this->assertEquals($expected, $this->storage->selectByListIds([5, 10, 3], ['id', 'title'], true));
        $expected = [
            5 => ['title' => 'five'],
            3 => ['title' => 'three'],
        ];
        $this->assertEquals($expected, $this->storage->selectByListIds([5, 3], ['title']));
        $this->assertEquals($expected, $this->storage->selectByListIds([5, 3], 'title'));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->selectByListIds([5, 3], ['title', 'unk']);
    }

    /**
     * @covers ::selectByField
     */
    public function testSelectByField()
    {
        $expected = [
            ['b' => 10],
            ['b' => 8],
            ['b' => 6],
        ];
        $this->assertEquals($expected, $this->storage->selectByField('a', 1, ['b'], 'id'));
        $expected = [
            ['b' => 8],
        ];
        $this->assertEquals($expected, $this->storage->selectByField('a', 1, ['b'], 'id', [1, 1]));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->selectByField('a', 1, ['b'], 'unk');
    }

    /**
     * @covers ::selectByWhere
     */
    public function testSelectByWhere()
    {
        $expected = [
            ['id' => 3, 'b' => 6],
            ['id' => 2, 'b' => 8],
            ['id' => 1, 'b' => 10],
        ];
        $actual = $this->storage->selectByWhere(['b' => [6, 7, 8, 10]], ['id', 'b'], ['id' => false]);
        $this->assertEquals($expected, $actual);
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->selectByWhere(['b' => [6, 7, 8, 10]], ['id', 'unk'], ['id' => false]);
    }

    /**
     * @covers ::updateById
     */
    public function testUpdateById()
    {
        $this->assertTrue($this->storage->updateById(4, ['title' => 'x', 'b' => 25]));
        $this->assertFalse($this->storage->updateById(44, ['title' => 'x', 'b' => 25]));
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'three', 1, 6],
            4 => [4, 'x', 2, 25],
            5 => [5, 'five', 2, 2],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->updateById(44, ['unk' => 'x', 'b' => 25]);
    }

    /**
     * @covers ::updateByListIds
     */
    public function testUpdateByListIds()
    {
        $this->assertSame(2, $this->storage->updateByListIds([1, 3, 5, 8], ['title' => 'three']));
        $expected = [
            1 => [1, 'three', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'three', 1, 6],
            4 => [4, 'four', 2, 4],
            5 => [5, 'three', 2, 2],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->updateById([44], ['unk' => 'x', 'b' => 25]);
    }

    /**
     * @covers ::updateByListIds
     */
    public function testUpdateByField()
    {
        $this->assertSame(2, $this->storage->updateByField('a', 2, ['b' => ['col' => 'b', 'value' => -2]]));
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'three', 1, 6],
            4 => [4, 'four', 2, 2],
            5 => [5, 'five', 2, 0],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->updateByField('zz', 44, [ 'b' => 25]);
    }

    /**
     * @covers ::updateByWhere
     */
    public function testUpdateByWhere()
    {
        $this->assertSame(3, $this->storage->updateByWhere(['a' => 1], ['title' => 'x']));
        $this->assertSame(2, $this->storage->updateByWhere(['a' => 2], ['title' => 'y']));
        $this->assertSame(0, $this->storage->updateByWhere(['a' => 3], ['title' => 'z']));
        $expected = [
            1 => [1, 'x', 1, 10],
            2 => [2, 'x', 1, 8],
            3 => [3, 'x', 1, 6],
            4 => [4, 'y', 2, 4],
            5 => [5, 'y', 2, 2],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->updateByWhere(['unk' => 1], ['title' => 'x']);
    }

    /**
     * @covers ::deleteById
     */
    public function testDeleteById()
    {
        $this->assertTrue($this->storage->deleteById(3));
        $this->assertFalse($this->storage->deleteById(33));
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
    }

    /**
     * @covers ::deleteByListIds
     */
    public function testDeleteByListIds()
    {
        $this->assertSame(2, $this->storage->deleteByListIds([2, 4, 6]));
        $expected = [
            1 => [1, 'one', 1, 10],
            3 => [3, 'three', 1, 6],
            5 => [5, 'five', 2, 2],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
    }

    /**
     * @covers ::deleteByField
     */
    public function testDeleteByField()
    {
        $this->assertSame(3, $this->storage->deleteByField('a', 1));
        $this->assertSame(0, $this->storage->deleteByField('a', 31));
        $expected = [
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->deleteByField('unk', 1);
    }

    /**
     * @covers ::deleteByWhere
     */
    public function testDeleteByWhere()
    {
        $this->assertSame(2, $this->storage->deleteByWhere(['b' => [8, 7, 4]]));
        $expected = [
            1 => [1, 'one', 1, 10],
            3 => [3, 'three', 1, 6],
            5 => [5, 'five', 2, 2],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->deleteByWhere(['bbb' => [8, 7, 4]]);
    }

    /**
     * @covers ::truncate
     */
    public function testTruncate()
    {
        $this->storage->truncate();
        $expected = [];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
    }

    /**
     * @covers ::insert
     */
    public function testInsert()
    {
        $set = [
            'title' => 'New',
            'a' => 10,
            'b' => 20,
        ];
        $id = $this->storage->insert($set);
        $this->assertInternalType('int', $id);
        $this->assertGreaterThan(5, $id);
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'three', 1, 6],
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
            $id => [$id, 'New', 10, 20],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->insert(['u' => 1]);
    }

    /**
     * @covers ::multiInsert
     */
    public function testMultiInsert()
    {
        $sets = [
            [
                'title' => 'First',
                'a' => 10,
                'b' => 20,
            ],
            [
                'title' => 'Second',
                'a' => 11,
                'b' => 22,
            ],
        ];
        $id = $this->storage->multiInsert($sets);
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'three', 1, 6],
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
            6 => [6, 'First', 10, 20],
            7 => [7, 'Second', 11, 22],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->multiInsert([['u' => 1]]);
    }

    /**
     * @covers ::replaceById
     */
    public function testReplaceById()
    {
        $this->storage->replaceById(3, ['title' => 'new']);
        $this->storage->replaceById(55, ['title' => 'ff']);
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'new', null, null],
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
            55 => [55, 'ff', null, null],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->replaceById(10, ['u' => 1]);
    }

    /**
     * @covers ::multiReplaceById
     */
    public function testMultiReplaceById()
    {
        $sets = [
            3 => ['title' => 'new'],
            55 => ['title' => 'ff'],
        ];
        $this->storage->multiReplaceById($sets);
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'new', null, null],
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
            55 => [55, 'ff', null, null],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->multiReplaceById([10 => ['u' => 1]]);
    }

    /**
     * @covers ::replace
     */
    public function testReplace()
    {
        $this->storage->replace(['id' => 3, 'title' => 'new']);
        $this->storage->replace(['id' => 55, 'title' => 'ff']);
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'new', null, null],
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
            55 => [55, 'ff', null, null],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->replace(['u' => 1]);
    }

    /**
     * @covers ::multiReplace
     */
    public function testMultiReplace()
    {
        $sets = [
            ['id' => 3, 'title' => 'new'],
            ['id' => 55, 'title' => 'ff'],
        ];
        $this->storage->multiReplace($sets);
        $expected = [
            1 => [1, 'one', 1, 10],
            2 => [2, 'two', 1, 8],
            3 => [3, 'new', null, null],
            4 => [4, 'four', 2, 4],
            5 => [5, 'five', 2, 2],
            55 => [55, 'ff', null, null],
        ];
        $pattern = 'SELECT `id_z`,`title_z`,`a`,`b_z` FROM `test` ORDER BY `id_z` ASC';
        $this->assertEquals($expected, $this->db->query($pattern)->numerics(0));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->multiReplace([['u' => 1]]);
    }

    /**
     * @covers ::countAll
     */
    public function testCountAll()
    {
        $this->assertEquals(5, $this->storage->countAll());
    }

    /**
     * @covers ::countByField
     */
    public function testCountByField()
    {
        $this->assertEquals(3, $this->storage->countByField('a', 1));
        $this->assertEquals(0, $this->storage->countByField('a', 3));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
        $this->storage->countByField('zz', 25);
    }

    /**
     * @covers ::countByWhere
     */
    public function testCountByWhere()
    {
        $this->assertEquals(2, $this->storage->countByWhere(['id' => [1, 2, 10]]));
        $this->setExpectedException('axy\trimdb\errors\QueryError');
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

    /**
     * @expectedException axy\trimdb\errors\InvalidStorageConfig
     */
    public function testConfigInvalid()
    {
        return new GoDBStorage([]);
    }

    /**
     * @covers ::getTable
     */
    public function testGetTable()
    {
        $table = $this->storage->getTable();
        $this->assertInstanceOf('go\DB\Table', $table);
        $this->assertSame('test', $table->getTableName());
    }
}
