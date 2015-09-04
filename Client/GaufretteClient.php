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
    private $filesystems;

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $fileName = explode('/', $archive);
        foreach ($this->filesystems as $filesystem) {
            $filesystem->write(end($fileName), file_get_contents($archive), true);
        }
    }

    /**
     * Setting Gaufrette filesystem according to bundle configurations.
     *
     * @param \Gaufrette\Filesystem $filesystem
     */
    public function addFilesystem(Filesystem $filesystem)
    {
        $this->filesystems[] = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Gaufrette';
    }
}
