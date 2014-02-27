<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\errors;

/**
 * Failed to query the repository
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class QueryError extends \axy\errors\Logic implements Query
{
    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'Failed to query the repository: "{{ errmsg }}"';

    /**
     * Constructor
     *
     * @param string $errmsg [optional]
     * @param mixed $storage
     * @param \Exception $previous [optional]
     * @param mixed $thrower [optional]
     */
    public function __construct($errmsg = null, $storage = null, \Exception $previous = null, $thrower = null)
    {
        $this->errmsg = $errmsg;
        $this->storage = $storage;
        $message = [
            'errmsg' => $errmsg,
            'storage' => $storage,
        ];
        parent::__construct($message, 0, $previous, $thrower);
    }

    /**
     * @return string
     */
    final public function getErrmsg()
    {
        return $this->errmsg;
    }

    /**
     * @return mixed
     */
    final public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @var string
     */
    protected $errmsg;

    /**
     * @var mixed
     */
    protected $storage;
}
