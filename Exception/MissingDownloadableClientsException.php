<?php
namespace Dizda\CloudBackupBundle\Exception;

class MissingDownloadableClientsException extends \LogicException
{
    /**
     * @return MissingDownloadableClientsException
     */
    public static function create()
    {
        return new self('No downloadable client is registered.');
    }
}
