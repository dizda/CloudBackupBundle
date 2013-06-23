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
class DropboxClient
{
    private $output;
    private $user;
    private $password;
    private $remotePath;

    /**
     * @param string $user       user
     * @param string $password   password
     * @param string $remotePath On Dropbox storage
     */
    public function __construct($user, $password, $remotePath)
    {
        $this->output     = new ConsoleOutput();
        $this->user       = $user;
        $this->password   = $password;
        $this->remotePath = $remotePath;
    }

    public function upload($archive)
    {
        $this->output->write('- <comment>Uploading to Dropbox...</comment>');

        $dropbox = new DropboxUploader($this->user, $this->password);
        $dropbox->upload($archive, $this->remotePath);

        $this->output->writeln('<info>OK</info>');
    }

}