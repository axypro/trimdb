<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\helpers;

/**
 * The interface of a map fields to cols
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
interface IColsMap
{
    /**
     * Converts a field name to a column name
     *
     * @param string $field
     * @return string
     */
    public function fromField($field);

    /**
     * Converts a column name to a field name
     *
     * @param string $col
     * @return string
     */
    public function toField($col);

    /**
     * Converts a plain list of field names to a list of column names
     *
     * @param array $list
     * @return array
     */
    public function fromList(array $list);

    /**
     * Converts a dictionary (fieldname => value) to (colname => value)
     *
     * @param array $dict
     * @return array
     */
    public function fromDict(array $dict);

    /**
     * Converts a list of dictionaries (such fromDict)
     *
     * @param array $mdict
     * @return array
     */
    public function fromMultiDict(array $mdict);

    /**
     * Converts a dictionary (colname => value) to (fieldname => value)
     *
     * @param array $cols
     * @return array
     */
    public function toDict(array $cols);
}
