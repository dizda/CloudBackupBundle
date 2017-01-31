<?php

namespace Dizda\CloudBackupBundle\Exception;

/**
 * Class UploadException
 */
class UploadException extends \Exception
{
    /**
     * @return UploadException
     */
    public function create()
    {
        return new self('Unable to upload the file.');
    }
}
