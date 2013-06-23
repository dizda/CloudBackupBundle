<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;

use Dizda\CloudBackupBundle\Clients\DropboxUploader;



class DropboxClient
{
    private $output;
    private $user;
    private $password;
    private $remotePath;


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