<?php
namespace Dizda\CloudBackupBundle\Client;

use Symfony\Component\Console\Output\ConsoleOutput;
use DropboxUploader;

/**
 * Class DropboxClient.
 * @deprecated Will be removed in 4.0. Please use DropboxSDKClient
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DropboxClient implements ClientInterface
{
    private $user;
    private $password;
    private $remotePath;

    /**
     * @param array $params user
     */
    public function __construct($params)
    {
        $params           = $params['dropbox'];
        $this->user       = $params['user'];
        $this->password   = $params['password'];
        $this->remotePath = $params['remote_path'];
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $dropbox = new DropboxUploader($this->user, $this->password);

        $dropbox->upload($archive, $this->remotePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Dropbox';
    }
}
