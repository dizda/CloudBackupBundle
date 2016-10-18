<?php

namespace Dizda\CloudBackupBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RestoreFailedEvent extends Event
{
    const RESTORE_FAILED = 'dizda.cloudbackup.restore_failed';

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
