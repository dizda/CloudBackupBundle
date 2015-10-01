<?php

namespace Dizda\CloudBackupBundle\Exception;

/**
 * Class UploadException
 */
class UploadException extends \Exception
{
    protected $message = 'Unable to upload the file.';
}
