<?php
namespace Dizda\CloudBackupBundle\Exception;

class RestoringNotAvailableException extends \LogicException
{
    /**
     * @return RestoringNotAvailableException
     */
    public static function create()
    {
        return new self('Restoring is not available.');
    }
}
