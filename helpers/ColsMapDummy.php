<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\helpers;

/**
 * Dummy implementation of IColsMap
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class ColsMapDummy implements IColsMap
{
    /**
     * {@inheritdoc}
     */
    public function fromField($field)
    {
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function toField($col)
    {
        return $col;
    }

    /**
     * {@inheritdoc}
     */
    public function fromList(array $list)
    {
        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function fromDict(array $dict)
    {
        return $dict;
    }

    /**
     * {@inheritdoc}
     */
    public function fromMultiDict(array $mdict)
    {
        return $mdict;
    }

    /**
     * {@inheritdoc}
     */
    public function toDict(array $cols)
    {
        return $cols;
    }
}
