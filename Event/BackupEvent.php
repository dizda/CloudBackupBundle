<?php

namespace Dizda\CloudBackupBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BackupEvent extends Event
{
    const BACKUP_COMPLETED = 'dizda.cloudbackup.backup_completed';
}
