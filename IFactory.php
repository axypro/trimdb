<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb;

/**
 * The interface of factory for building storages
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
interface IFactory
{
    /**
     * Creates a storage by parameters
     *
     * @param mixed $params
     * @return \axy\trimdb\IStorage
     */
    public function createStorage($params);
}
