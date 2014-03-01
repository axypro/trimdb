<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\tests;

use axy\trimdb\Factory;
use go\DB\DB;
use go\DB\Storage as DBStorage;

/**
 * @coversDefaultClass axy\trimdb\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::createStorage
     */
    public function testCreateFromStorage()
    {
        $db1 = DB::create(['host' => 'localhost'], 'test');
        $db2 = DB::create(['host' => 'localhost'], 'test');
        $dbstorage = new DBStorage();
        $dbstorage->set($db1);
        $dbstorage->set($db2, 'two');
        $config = [
            'dbstorage' => $dbstorage,
        ];
        $factory = new Factory($config);
        $params1 = [
            'table' => 'first',
            'map' => [
                'x' => 'y',
            ],
        ];
        $storage1 = $factory->createStorage($params1);
        $this->assertInstanceOf('axy\trimdb\storages\GoDBStorage', $storage1);
        $table1 = $storage1->getTable();
        $this->assertSame('first', $table1->getTableName());
        $this->assertSame($db1, $table1->getDB());
        $this->assertEquals(['x' => 'y'], $table1->getMap()->getMap());
        $params2 = [
            'table' => 'second',
            'db' => 'two',
        ];
        $storage2 = $factory->createStorage($params2);
        $this->assertInstanceOf('axy\trimdb\storages\GoDBStorage', $storage2);
        $table2 = $storage2->getTable();
        $this->assertSame('second', $table2->getTableName());
        $this->assertSame($db2, $table2->getDB());
        $this->assertNull($table2->getMap());
    }

    /**
     * @covers ::createStorage
     */
    public function testCreateFromDB()
    {
        $db = DB::create(['host' => 'localhost'], 'test');
        $config = [
            'db' => $db,
        ];
        $factory = new Factory($config);
        $params1 = [
            'table' => 'first',
            'map' => [
                'x' => 'y',
            ],
        ];
        $storage1 = $factory->createStorage($params1);
        $this->assertInstanceOf('axy\trimdb\storages\GoDBStorage', $storage1);
        $table1 = $storage1->getTable();
        $this->assertSame('first', $table1->getTableName());
        $this->assertSame($db, $table1->getDB());
        $this->assertEquals(['x' => 'y'], $table1->getMap()->getMap());
        $params2 = [
            'table' => 'second',
            'db' => 'two',
        ];
        $storage2 = $factory->createStorage($params2);
        $this->assertInstanceOf('axy\trimdb\storages\GoDBStorage', $storage2);
        $table2 = $storage2->getTable();
        $this->assertSame('second', $table2->getTableName());
        $this->assertSame($db, $table2->getDB());
        $this->assertNull($table2->getMap());
    }

    /**
     * @covers ::createStorage
     */
    public function testCreateEmpty()
    {
        $config = [
            'empty' => true,
            'x' => 1,
            'y' => 2,
        ];
        $factory = new Factory($config);
        $params = [
            'data' => [
                1 => ['x' => 1],
            ],
            'y' => 3,
            'z' => 4,
        ];
        $storage = $factory->createStorage($params);
        $this->assertInstanceOf('axy\trimdb\storages\EmptyStorage', $storage);
        $expected = [
            'empty' => true,
            'data' => [
                1 => ['x' => 1],
            ],
            'x' => 1,
            'y' => 3,
            'z' => 4,
        ];
        $this->assertEquals($expected, $storage->getConfig());
    }

    /**
     * @covers ::createStorage
     * @dataProvider providerInvalidFactoryConfig
     * @param array $config
     * @expectedException axy\trimdb\errors\InvalidFactoryConfig
     */
    public function testInvalidFactoryConfig($config)
    {
        return new Factory($config);
    }

    /**
     * @return array
     */
    public function providerInvalidFactoryConfig()
    {
        return [
            [
                [
                ],
            ],
            [
                [
                    'x' => 1,
                ],
            ],
            [
                [
                    'dbstorage' => 'x',
                ],
            ],
            [
                [
                    'db' => 'y',
                ],
            ],
        ];
    }

    /**
     * @covers ::createStorage
     * @dataProvider providerInvalidStorageConfig
     * @param array $fconfig
     * @param array $params
     * @expectedException axy\trimdb\errors\InvalidStorageConfig
     */
    public function testInvalidStorageConfig($fconfig, $params)
    {
        $factory = new Factory($fconfig);
        $factory->createStorage($params);
    }

    /**
     * @return array
     */
    public function providerInvalidStorageConfig()
    {
        $db = DB::create(['host' => 'localhost'], 'test');
        $dbstorage = new DBStorage();
        $dbstorage->set($db);
        return [
            [
                [
                    'db' => $db,
                ],
                10,
            ],
            [
                [
                    'empty' => true,
                ],
                10,
            ],
            [
                [
                    'db' => $db,
                ],
                [
                ],
            ],
            [
                [
                    'dbstorage' => $dbstorage,
                ],
                [
                    'table' => 'qwerty',
                    'db' => 'unknown',
                ],
            ],
        ];
    }
}
