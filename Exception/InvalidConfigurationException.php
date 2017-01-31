<?php
namespace Dizda\CloudBackupBundle\Exception;

class InvalidConfigurationException extends \LogicException
{
    /**
     * @return InvalidConfigurationException
     */
    public static function create($param)
    {
        return new self(sprintf('Parameter "%s" is not set.', $param));
    }
}
