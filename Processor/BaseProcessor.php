<?php

namespace Dizda\CloudBackupBundle\Processor;

/**
 * @author Tobias Nyholm
 */
abstract class BaseProcessor
{
    /**
     * @var array options
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Add new options to the existing once. This may overwrite.
     *
     * @param array $options
     *
     * @return $this
     */
    public function addOptions($options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }
}
