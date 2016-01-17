<?php
namespace Dizda\CloudBackupBundle\Client;

use League\Flysystem\FilesystemInterface;

/**
 * Client for Flysystem adapters.
 *
 * @author Marc Aubé
 */
class FlysystemClient implements ClientInterface
{
    /**
     * @var FilesystemInterface[]
     */
    private $filesystems;

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $fileName = explode('/', $archive);

        /** @var FilesystemInterface $filesystem */
        foreach ($this->filesystems as $filesystem) {
            $filesystem->write(end($fileName), file_get_contents($archive));
        }
    }

    /**
     * Add a filesystem adapter.
     *
     * @param FilesystemInterface $filesystem
     */
    public function addFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystems[] = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Flysystem';
    }
}
