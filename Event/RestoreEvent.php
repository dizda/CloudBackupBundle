<?php

namespace Dizda\CloudBackupBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RestoreEvent extends Event
{
    const RESTORE_COMPLETED = 'dizda.cloudbackup.restore_completed';

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @param \SplFileInfo $file
     */
    public function __construct(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }
}
