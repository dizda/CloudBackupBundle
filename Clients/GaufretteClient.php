<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;

use Gaufrette\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class GaufretteClient
 * Client for Gaufrette drivers
 *
 * @package Dizda\CloudBackupBundle\Clients
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class GaufretteClient implements ClientInterface
{
    private $output;
    private $filesystem;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->output     = new ConsoleOutput();
    }

    public function upload($archive)
    {
        $this->output->write('- <comment>Uploading using Gaufrette... </comment>');
        if(is_array($archive)){
            $this->output->writeln("");
            foreach($archive as $file /* @var $file SplFileInfo*/){
                $this->output->write(sprintf('----- <comment>Uploading file: %s... </comment>', $file->getFilename()));
                $fileName = explode('/', $file);
                $this->filesystem->write(end($fileName), file_get_contents($file), true);
                $this->output->writeln('<info>OK</info>');
            }
        }
        else{
            $fileName = explode('/', $archive);
            $this->filesystem->write(end($fileName), file_get_contents($archive), true);
            $this->output->writeln('<info>OK</info>');
        }
    }

    /**
     * Setting gaufrette filesystem according to bundle configurations
     *
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

}