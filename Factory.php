<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb;

use axy\trimdb\storages\GoDBStorage;
use axy\trimdb\storages\EmptyStorage;
use axy\trimdb\errors\InvalidFactoryConfig;
use axy\trimdb\errors\InvalidStorageConfig;
use go\DB\DB;
use go\DB\Storage as DBStorage;
use go\DB\Exceptions\StorageNotFound;

/**
 * The standard implementation of IFactory
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class Factory implements IFactory
{
    /**
     * Constructor
     *
     * @param array $config
     * @throws \axy\trimdb\errors\InvalidFactoryConfig
     */
    public function __construct(array $config)
    {
        if (!empty($config['empty'])) {
            $this->empty = $config;
        } elseif (isset($config['dbstorage'])) {
            if (!($config['dbstorage'] instanceof DBStorage)) {
                throw new InvalidFactoryConfig('dbstorage is not instance of go\DB\Storage');
            }
            $this->dbstorage = $config['dbstorage'];
        } elseif (isset($config['db'])) {
            if (!($config['db'] instanceof DB)) {
                throw new InvalidFactoryConfig('db is not instance of go\DB\DB');
            }
            $this->db = $config['db'];
        } else {
            throw new InvalidFactoryConfig('required fields was not found');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createStorage($params)
    {
        if (!\is_array($params)) {
            throw new InvalidStorageConfig('config must be an array');
        }
        if ($this->empty) {
            return new EmptyStorage(\array_replace($this->empty, $params));
        }
        if ($this->dbstorage) {
            try {
                if (isset($params['db'])) {
                    $db = $this->dbstorage->get($params['db']);
                } else {
                    $db = $this->dbstorage->getMainDB();
                }
            } catch (StorageNotFound $e) {
                throw new InvalidStorageConfig($e->getMessage(), $e);
            }
        } else {
            $db = $this->db;
        }
        if (!isset($params['table'])) {
            throw new InvalidStorageConfig('required table name');
        }
        $map = isset($params['map']) ? $params['map'] : null;
        $config = [
            'table' => $db->getTable($params['table'], $map),
        ];
        if (isset($params['imp'])) {
            $config['imp'] = $params['imp'];
        }
        return new GoDBStorage($config);
    }

    /**
     * @var \go\DB\DB
     */
    private $db;

    /**
     * @var \go\DB\Storage
     */
    private $dbstorage;

    /**
     * @var array
     */
    private $empty;
}
