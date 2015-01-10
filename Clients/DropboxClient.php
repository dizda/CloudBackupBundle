<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;

use Dizda\CloudBackupBundle\Clients\DropboxUploader;


/**
 * Class DropboxClient
 *
 * @package Dizda\CloudBackupBundle\Clients
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DropboxClient implements ClientInterface
{
    private $output;
    private $user;
    private $password;
    private $remotePath;

    /**
     * @param array  $params user
     */
    public function __construct($params)
    {
        $this->output     = new ConsoleOutput();
        $params           = $params['dropbox'];
        $this->user       = $params['user'];
        $this->password   = $params['password'];
        $this->remotePath = $params['remote_path'];
    }

    public function upload($archive)
    {
        $this->output->write('- <comment>Uploading to Dropbox...</comment>');

        $dropbox = new DropboxUploader($this->user, $this->password);
        $dropbox->upload($archive, $this->remotePath);

        $this->output->writeln('<info>OK</info>');
    }

}