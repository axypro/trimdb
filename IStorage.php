<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb;

/**
 * The interface of data storage
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
interface IStorage
{
    /**
     * Selects a row by an ID
     *
     * @param int $id
     * @param array $cols [optional]
     *        required columns
     * @return array
     *         a fields list of the row or NULL if it is not was found
     * @throws \axy\trimdb\errors\Query
     */
    public function selectById($id, $cols = null);

    /**
     * Selects the rows that ID is in a list
     *
     * @param array $ids
     *        a list of IDs
     * @param array $cols [optional]
     *        required columns
     * @param boolean $onlyex [optional]
     *        not return a non-existent elements (by default, return NULL)
     * @return array
     *         id => fields (sorted as $ids)
     * @throws \axy\trimdb\errors\Query
     */
    public function selectByListIds(array $ids, $cols = null, $onlyex = false);

    /**
     * Selects the rows that $field=$value
     *
     * @param string $field
     *        a field name
     * @param mixed $value
     *        desired value of the field
     * @param array $cols [optional]
     *        required columns
     * @param mixed $order [optional]
     *        the order of result
     * @param mixed $limit [optional]
     *        the limit the returned list
     * @param string $key [optional]
     *        a name of field from result, for the resulting array key
     * @return array
     * @throws \axy\trimdb\errors\Query
     */
    public function selectByField($field, $value, $cols = null, $order = null, $limit = null, $key = null);

    /**
     * Selects the rows that corresponding to conditions
     *
     * @param mixed $where
     *        the conditions
     * @param array $cols [optional]
     *        required columns
     * @param mixed $order [optional]
     *        the order of result
     * @param mixed $limit [optional]
     *        the limit the returned list
     * @param string $key [optional]
     *        a name of field from result, for the resulting array key
     * @return array
     * @throws \axy\trimdb\errors\Query
     */
    public function selectByWhere($where, $cols = null, $order = null, $limit = null, $key = null);

    /**
     * Updates a row by a ID
     *
     * @param int $id
     * @param mixed $set
     * @return boolean
     *         TRUE if the row was updated
     * @throws \axy\trimdb\errors\Query
     */
    public function updateById($id, $set);

    /**
     * Updates the rows that ID is in a list
     *
     * @param array $ids
     * @param mixed $set
     * @return int
     *         count of affected rows
     * @throws \axy\trimdb\errors\Query
     */
    public function updateByListIds(array $ids, $set);

    /**
     * Updates the rows that $field=$value
     *
     * @param string $field
     * @param mixed $value
     * @param mixed $set
     * @return int
     *         count of affected rows
     * @throws \axy\trimdb\errors\Query
     */
    public function updateByField($field, $value, $set);

    /**
     * Updates the rows that match the $where-conditions
     *
     * @param mixed $where
     * @param array $set
     * @return int
     *         count of affected rows
     * @throws \axy\trimdb\errors\Query
     */
    public function updateByWhere($where, $set);

    /**
     * Deletes a row by ID
     *
     * @param int $id
     * @return boolean
     *         the row was deleted
     * @throws \axy\trimdb\errors\Query
     */
    public function deleteById($id);

    /**
     * Deletes a list of rows by a list of id
     *
     * @param array $ids
     * @return int
     *         count of affected rows
     * @throws \axy\trimdb\errors\Query
     */
    public function deleteByListIds(array $ids);

    /**
     * Deletes a list of rows by a field value
     *
     * @param string $field
     * @param mixed $value
     * @return int
     *         count of affected rows
     * @throws \axy\trimdb\errors\Query
     */
    public function deleteByField($field, $value);

    /**
     * Deletes the rows that match the $where-conditions
     *
     * @param mixed $where
     * @return int
     *         count of affected rows
     * @throws \axy\trimdb\errors\Query
     */
    public function deleteByWhere($where);

    /**
     * Clears the storage
     *
     * @throws \axy\trimdb\errors\Query
     */
    public function truncate();

    /**
     * Creates a row
     *
     * @param mixed $set
     *        the row fields
     * @return int
     *         ID of the row
     * @throws \axy\trimdb\errors\Query
     */
    public function insert($set);

    /**
     * Creates a rows list
     *
     * @param array $sets
     * @throws \axy\trimdb\errors\Query
     */
    public function multiInsert(array $sets);

    /**
     * Replaces a row fields by ID
     *
     * @param int $id
     * @param mixed $set
     * @throws \axy\trimdb\errors\Query
     */
    public function replaceById($id, $set);

    /**
     * Replaces fields for a list of rows (by ID)
     *
     * @param array $sets
     * @throws \axy\trimdb\errors\Query
     */
    public function multiReplaceById(array $sets);

    /**
     * Replaces a row fields by PK
     *
     * @param array $set
     * @throws \axy\trimdb\errors\Query
     */
    public function replace($set);

    /**
     * Replaces fields for a list of rows (by PK)
     *
     * @param array $sets
     * @throws \axy\trimdb\errors\Query
     */
    public function multiReplace(array $sets);

    /**
     * Returns the count of all rows
     *
     * @return int
     * @throws \axy\trimdb\errors\Query
     */
    public function countAll();

    /**
     * Returns the count of rows that $field=$value
     *
     * @param string $field
     * @param mixed $value
     * @return int
     * @throws \axy\trimdb\errors\Query
     */
    public function countByField($field, $value);

    /**
     * Returns the count of rows by $where-conditions
     *
     * @param mixed $where
     * @return int
     * @throws \axy\trimdb\errors\Query
     */
    public function countByWhere($where);

    /**
     * Checks if a row is exists
     *
     * @param int $id
     * @return boolean
     * @throws \axy\trimdb\errors\Query
     */
    public function existsId($id);
}
