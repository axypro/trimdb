<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\storages;

use axy\trimdb\errors\QueryError;

/**
 * Empty storages for dummy and tests
 *
 * Config
 * "data" [optional] - test data (id => row)
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class EmptyStorage extends BaseStorage
{
    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['data']) && \is_array($config['data'])) {
            $this->data = $config['data'];
            $this->maxid = \max(\array_keys($this->data));
        } else {
            $this->data = [];
        }
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function selectById($id, $cols = null)
    {
        if (!isset($this->data[$id])) {
            return null;
        }
        $row = $this->data[$id];
        if ((!\is_array($cols)) || empty($cols)) {
            return $row;
        }
        $result = [];
        foreach ($cols as $col) {
            if (!\array_key_exists($col, $row)) {
                throw new QueryError($col.' is not found', $this);
            }
            $result[$col] = $row[$col];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function selectByListIds(array $ids, $cols = null, $onlyex = false)
    {
        $result = [];
        foreach ($ids as $id) {
            $item = $this->selectById($id, $cols);
            if ($item) {
                $result[$id] = $item;
            } elseif (!$onlyex) {
                $result[$id] = null;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updateById($id, $set)
    {
        if (!isset($this->data[$id])) {
            return false;
        }
        $row = $this->data[$id];
        $this->data[$id] = \array_replace($row, $set);
        return ($row !== $this->data[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateByListIds(array $ids, $set)
    {
        $count = 0;
        foreach ($ids as $id) {
            if ($this->updateById($id, $set)) {
                $count += 1;
            }
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        if (isset($this->data[$id])) {
            unset($this->data[$id]);
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByListIds(array $ids)
    {
        $count = 0;
        foreach ($ids as $id) {
            if ($this->deleteById($id)) {
                $count += 1;
            }
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function truncate()
    {
        $this->data = [];
        $this->maxid = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($set)
    {
        $this->maxid++;
        $set['id'] = $this->maxid;
        $this->data[$this->maxid] = $set;
        return $this->maxid;
    }

    /**
     * {@inheritdoc}
     */
    public function replace($set)
    {
        if (!isset($set['id'])) {
            $this->insert($set);
            return;
        }
        $id = $set['id'];
        if (isset($this->data[$id])) {
            $this->data[$id] = $set;
            return;
        }
        $this->data[$id] = $set;
        if ($id > $this->maxid) {
            $this->maxid = $id;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function countAll()
    {
        return \count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function existsId($id)
    {
        return isset($this->data[$id]);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @var array
     */
    protected $data;

    /**
     * @var int
     */
    protected $maxid = 0;

    /**
     * @var array
     */
    protected $config;
}
