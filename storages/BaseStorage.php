<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\storages;

use axy\trimdb\IStorage;
use axy\trimdb\errors\NotSupported;

abstract class BaseStorage implements IStorage
{
    /**
     * {@inheritdoc}
     */
    public function selectById($id, $cols = null)
    {
        $result = $this->selectByWhere(['id' => $id], $cols, null, 1);
        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function selectByListIds(array $ids, $cols = null, $onlyex = false)
    {
        $eid = false;
        if (!empty($cols)) {
            if (\is_array($cols)) {
                if (!\in_array('id', $cols)) {
                    $eid = true;
                    $cols[] = 'id';
                }
            } elseif ($cols !== 'id') {
                $eid = true;
                $cols = [$cols, 'id'];
            }
        }
        $rows = $this->selectByWhere(['id' => $ids], $cols, null, null, 'id');
        $result = [];
        foreach ($ids as $id) {
            if (isset($rows[$id])) {
                $row = $rows[$id];
                if ($eid) {
                    unset($row['id']);
                }
                $result[$id] = $row;
            } elseif (!$onlyex) {
                $result[$id] = null;
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function selectByField($field, $value, $cols = null, $order = null, $limit = null, $key = null)
    {
        return $this->selectByWhere([$field => $value], $cols, $order, $limit, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function selectByWhere($where, $cols = null, $order = null, $limit = null, $key = null)
    {
        throw new NotSupported($this->getCurrentAction(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function updateById($id, $set)
    {
        return ($this->updateByWhere(['id' => $id], $set) > 0);
    }

    /**
     * {@inheritdoc}
     */
    public function updateByListIds(array $ids, $set)
    {
        return $this->updateByWhere(['id' => $ids], $set);
    }

    /**
     * {@inheritdoc}
     */
    public function updateByField($field, $value, $set)
    {
        return $this->updateByWhere([$field => $value], $set);
    }

    /**
     * {@inheritdoc}
     */
    public function updateByWhere($where, $set)
    {
        throw new NotSupported($this->getCurrentAction(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return ($this->deleteByWhere(['id' => $id]) > 0);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByListIds(array $ids)
    {
        return $this->deleteByWhere(['id' => $ids]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByField($field, $value)
    {
        return $this->deleteByWhere([$field => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByWhere($where)
    {
        throw new NotSupported($this->getCurrentAction(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function truncate()
    {
        $this->deleteByWhere(true);
    }

    /**
     * {@inheritdoc}
     */
    public function insert($set)
    {
        throw new NotSupported($this->getCurrentAction(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function multiInsert(array $sets)
    {
        foreach ($sets as $set) {
            $this->insert($set);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replaceById($id, $set)
    {
        $set['id'] = $id;
        return $this->replace($set);
    }

    /**
     * {@inheritdoc}
     */
    public function multiReplaceById(array $sets)
    {
        $nsets = [];
        foreach ($sets as $k => $set) {
            $set['id'] = $k;
            $nsets[] =  $set;
        }
        return $this->multiReplace($nsets);
    }

    /**
     * {@inheritdoc}
     */
    public function replace($set)
    {
        throw new NotSupported($this->getCurrentAction(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function multiReplace(array $sets)
    {
        foreach ($sets as $set) {
            $this->replace($set);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function countAll()
    {
        return $this->countByWhere(true);
    }

    /**
     * {@inheritdoc}
     */
    public function countByField($field, $value)
    {
        return $this->countByWhere([$field => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function countByWhere($where)
    {
        throw new NotSupported($this->getCurrentAction(), $this);
    }

    /**
     * {@inheritdoc}
     */
    public function existsId($id)
    {
        return (bool)$this->countByWhere(['id' => $id]);
    }

    /**
     * @return string
     */
    protected function getCurrentAction()
    {
        $action = null;
        foreach (\debug_backtrace() as $item) {
            if (isset($item['class']) && (\strpos($item['class'], __NAMESPACE__) === 0)) {
                $action = isset($item['function']) ? $item['function'] : null;
            }
        }
        return $action;
    }
}
