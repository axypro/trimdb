<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\helpers;

/**
 * Working implementation of IColsMap
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ColsMap implements IColsMap
{
    /**
     * Constructor
     *
     * @param array $cols
     */
    public function __construct(array $cols)
    {
        $this->cols = $cols;
    }

    /**
     * {@inheritdoc}
     */
    public function fromField($field)
    {
        return isset($this->cols[$field]) ? $this->cols[$field] : $field;
    }

    /**
     * {@inheritdoc}
     */
    public function toField($col)
    {
        if (!$this->flip) {
            $this->flip = \array_flip($this->cols);
        }
        return isset($this->flip[$col]) ? $this->flip[$col] : $col;
    }

    /**
     * {@inheritdoc}
     */
    public function fromList(array $list)
    {
        $cols = [];
        foreach ($list as $field) {
            $cols[] = isset($this->cols[$field]) ? $this->cols[$field] : $field;
        }
        return $cols;
    }

    /**
     * {@inheritdoc}
     */
    public function fromDict(array $dict)
    {
        $cols = [];
        foreach ($dict as $field => $v) {
            if (isset($this->cols[$field])) {
                $field = $this->cols[$field];
            }
            $cols[$field] = $v;
        }
        return $cols;
    }

    /**
     * {@inheritdoc}
     */
    public function fromMultiDict(array $mdict)
    {
        $mcols = [];
        foreach ($mdict as $mk => $dict) {
            $cols = [];
            foreach ($dict as $field => $v) {
                if (isset($this->cols[$field])) {
                    $field = $this->cols[$field];
                }
                $cols[$field] = $v;
            }
            $mcols[$mk] = $cols;
        }
        return $mcols;
    }

    /**
     * {@inheritdoc}
     */
    public function toDict(array $rows)
    {
        if (!$this->flip) {
            $this->flip = \array_flip($this->cols);
        }
        $result = [];
        foreach ($rows as $mk => $row) {
            $dict = [];
            foreach ($row as $k => $v) {
                if (isset($this->flip[$k])) {
                    $k = $this->flip[$k];
                }
                $dict[$k] = $v;
            }
            $result[$mk] = $dict;
        }
        return $result;
    }

    /**
     * @var array
     */
    private $cols;

    /**
     * @var array
     */
    private $flip;
}
