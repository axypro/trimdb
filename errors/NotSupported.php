<?php
/**
 * @package axy\trimdb
 */

namespace axy\trimdb\errors;

/**
 * The current storage is not supported this action
 *
 * @author Oleg Grigoriev <go.vasac@gmail.com>
 */
class NotSupported extends \axy\errors\Logic implements Query
{
    /**
     * {@inheritdoc}
     */
    protected $defaultMessage = 'Storage is not supported action "{{ action }}"';

    /**
     * Constructor
     *
     * @param string $action [optional]
     * @param mixed $storage
     * @param \Exception $previous [optional]
     * @param mixed $thrower [optional]
     */
    public function __construct($action = null, $storage = null, \Exception $previous = null, $thrower = null)
    {
        $this->action = $action;
        $this->storage = $storage;
        $message = [
            'action' => $action,
            'storage' => $storage,
        ];
        parent::__construct($message, 0, $previous, $thrower);
    }

    /**
     * @return string
     */
    final public function getAction()
    {
        return $this->action;
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
    protected $action;

    /**
     * @var mixed
     */
    protected $storage;
}
