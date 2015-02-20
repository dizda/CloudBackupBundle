<?php
namespace Dizda\CloudBackupBundle\Client;

use Symfony\Component\Console\Output\ConsoleOutput;
use Gaufrette\Filesystem;

/**
 * Class GaufretteClient
 * Client for Gaufrette drivers.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class GaufretteClient implements ClientInterface
{
    private $output;
    private $filesystem;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->output     = new ConsoleOutput();
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $fileName = explode('/', $archive);
        $this->filesystem->write(end($fileName), file_get_contents($archive), true);
    }

    /**
     * Setting Gaufrette filesystem according to bundle configurations.
     *
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Gaufrette';
    }
}
