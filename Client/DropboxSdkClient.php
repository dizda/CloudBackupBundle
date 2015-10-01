<?php
namespace Dizda\CloudBackupBundle\Client;

use Dizda\CloudBackupBundle\Exception\UploadException;
use \Dropbox as Dropbox;

/**
 * Class DropboxSdkClient.
 *
 * @author David Fuertes
 */
class DropboxSdkClient implements ClientInterface
{
    /**
     * @var string
     */
    private $access_token;

    /**
     * @param array $params user
     */
    public function __construct($params)
    {
        $params             = $params['dropbox_sdk'];
        $this->access_token = $params['access_token'];
        $this->remotePath   = $params['remote_path'];
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $fileName  = explode('/', $archive);
        $pathError = Dropbox\Path::findErrorNonRoot($this->remotePath);

        if ($pathError !== null) {
            throw new UploadException(sprintf('Invalid path "%s".', $archive));
        }

        $client = new Dropbox\Client($this->access_token, 'CloudBackupBundle');
        $size   = filesize($archive);

        $fp = fopen($archive, 'rb');
        $client->uploadFile($this->remotePath.'/'.end($fileName), Dropbox\WriteMode::add(), $fp, $size);
        fclose($fp);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'DropboxSdk';
    }
}
