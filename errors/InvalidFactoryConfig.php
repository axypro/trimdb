<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\errors;

/**
 * A factory config is invalid
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class InvalidFactoryConfig extends \axy\errors\InvalidConfig implements InvalidConfig
{
    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'A trimdb-factory config has an invalid format: "{{ errmsg }}"';

    /**
     * Constructor
     *
     * @param string $errmsg [optional]
     * @param \Exception $previous [optional]
     * @param mixed $thrower [optional]
     */
    public function __construct($errmsg = null, \Exception $previous = null, $thrower = null)
    {
        parent::__construct('trimdb-factory', $errmsg, 0, $previous, $thrower);
    }
}
