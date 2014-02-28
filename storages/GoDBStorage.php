<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\storages;

use axy\trimdb\errors\InvalidStorageConfig;
use axy\trimdb\errors\QueryError;
use go\DB\Table;
use go\DB\Exceptions\Exception as DBError;

/**
 * IStorage implementation for goDB
 *
 * Config:
 * "table" (go\DB\Table)
 * "imp" (array) ["minsert", "truncate"]
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class GoDBStorage extends BaseStorage
{
    /**
     * Constructor
     *
     * @param array $config
     * @throws \axy\trimdb\errors\InvalidStorageConfig
     */
    public function __construct(array $config)
    {
        if ((!isset($config['table'])) || (!($config['table'] instanceof Table))) {
            throw new InvalidStorageConfig('require table');
        }
        $this->table = $config['table'];
        if (isset($config['imp']) && \is_array($config['imp'])) {
            $this->imp = \array_replace($this->imp, $config['imp']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function selectByWhere($where, $cols = null, $order = null, $limit = null, $key = null)
    {
        try {
            return $this->table->select($cols, $where, $order, $limit)->assoc($key);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateByWhere($where, $set)
    {
        try {
            return $this->table->update($set, $where);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByWhere($where)
    {
        try {
            return $this->table->delete($where);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function truncate()
    {
        if ($this->imp['truncate']) {
            $this->table->truncate(true);
        } else {
            $this->deleteByWhere(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function insert($set)
    {
        try {
            return $this->table->insert($set);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function multiInsert(array $sets)
    {
        try {
            return $this->table->multiInsert($sets, $this->imp['minsert']);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replace($set)
    {
        try {
            return $this->table->replace($set);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function multiReplace(array $sets)
    {
        try {
            return $this->table->multiReplace($sets, $this->imp['minsert']);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function countByWhere($where)
    {
        try {
            return (int)$this->table->getCount(null, $where);
        } catch (DBError $e) {
            throw new QueryError($e->getMessage(), $this, $e);
        }
    }

    /**
     * @var \go\DB\Table
     */
    protected $table;

    /**
     * @var array
     */
    protected $imp = [
        'minsert' => true,
        'truncate' => true,
    ];
}
