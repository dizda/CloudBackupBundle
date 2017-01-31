<?php
namespace Dizda\CloudBackupBundle\Client;

use Dizda\CloudBackupBundle\Exception\InvalidConfigurationException;
use Gaufrette\Filesystem;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

/**
 * Class GaufretteClient
 * Client for Gaufrette drivers.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class GaufretteClient implements ClientInterface, DownloadableClientInterface
{
    /**
     * @var Filesystem[]
     */
    private $filesystems;

    /**
     * @var string
     */
    private $restoreFolder;

    /**
     * @var LocalFilesystem
     */
    private $localFilesystem;

    /**
     * @param string $restoreFolder
     * @param LocalFilesystem $localFilesystem
     */
    public function __construct($restoreFolder = null, LocalFilesystem $localFilesystem = null)
    {
        $this->restoreFolder = $restoreFolder;
        $this->localFilesystem = $localFilesystem;
    }

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
     * @return Filesystem
     */
    private function getFirstFilesystem()
    {
        return $this->filesystems[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Gaufrette';
    }

    /**
     * {@inheritdoc}
     */
    public function download()
    {
        if (!$this->restoreFolder) {
            throw InvalidConfigurationException::create('$restoreFolder');
        }
        $fileSystem = $this->getFirstFilesystem();

        $files = $fileSystem->keys();
        $fileName = end($files);

        $content = $fileSystem->get($fileName)->getContent();
        $splFile = new \SplFileInfo($this->restoreFolder . $fileName);

        $this->localFilesystem->dumpFile($splFile->getPathname(), $content);

        return $splFile;
    }
}
