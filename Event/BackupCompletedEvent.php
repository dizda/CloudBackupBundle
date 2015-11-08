<?php

namespace Dizda\CloudBackupBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BackupCompletedEvent extends Event
{
    const EVENT_NAME = 'backup_completed';
}
