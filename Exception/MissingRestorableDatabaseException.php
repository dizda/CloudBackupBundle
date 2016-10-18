<?php
namespace Dizda\CloudBackupBundle\Exception;

class MissingRestorableDatabaseException extends \LogicException
{
    /**
     * @return MissingRestorableDatabaseException
     */
    public static function create()
    {
        return new self('No restorable database is registered.');
    }
}
