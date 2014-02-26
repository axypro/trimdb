<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\errors;

/**
 * A storage config is invalid
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class InvalidStorageConfig extends \axy\errors\InvalidConfig implements InvalidConfig
{
    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'A trimdb-storage config has an invalid format: "{{ errmsg }}"';

    /**
     * Constructor
     *
     * @param string $errmsg [optional]
     * @param \Exception $previous [optional]
     * @param mixed $thrower [optional]
     */
    public function __construct($errmsg = null, \Exception $previous = null, $thrower = null)
    {
        parent::__construct('trimdb-storage', $errmsg, 0, $previous, $thrower);
    }
}
