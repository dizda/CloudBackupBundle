<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;

use Gaufrette\Filesystem;

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
        $this->output->write('- <comment>Uploading using Gaufrette...</comment>');

        $fileName = explode('/', $archive);

        $this->filesystem->write(end($fileName), file_get_contents($archive), true);

        $this->output->writeln('<info>OK</info>');
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