<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;

use DropboxUploader;
use Symfony\Component\Finder\SplFileInfo;

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
        $this->output->write('- <comment>Uploading to Dropbox... </comment>');
        $dropbox = new DropboxUploader($this->user, $this->password);
        if(is_array($archive)){
            $this->output->writeln("");
            foreach($archive as $file /* @var $file SplFileInfo*/){
                $this->output->write(sprintf('----- <comment>Uploading file: %s... </comment>', $file->getFilename()));
                $dropbox->upload($file, $this->remotePath);
                $this->output->writeln('<info>OK</info>');
            }
        }
        else{
            $dropbox->upload($archive, $this->remotePath);
            $this->output->writeln('<info>OK</info>');
        }
    }

}
