<?php
namespace Dizda\CloudBackupBundle\Databases;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class BaseDatabase
 *
 * @package Dizda\CloudBackupBundle\Databases
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
abstract class BaseDatabase
{
    const DB_PATH = '';

    protected $filePrefix;
    protected $kernelCacheDir;
    protected $filesystem;
    protected $basePath;
    protected $dataPath;
    protected $archivePath;
    protected $compressedArchivePath;


    /**
     * Get SF2 Filesystem
     */
    public function __construct($filePrefix)
    {
        $this->filePrefix = $filePrefix;
        $this->filesystem = new Filesystem();
    }


    /**
     * Preparation of directory
     *
     * $this->basePath      /Users/high/Sites/dizdabundles/app/cache/dev/db/
     * $this->dataPath      /Users/high/Sites/dizdabundles/app/cache/dev/db/mongo/
     * $this->archivePath   /Users/high/Sites/dizdabundles/app/cache/dev/db/bambou_2013_01_12-01_36_33.tar
     *
     * TODO: Add a config prefix to archive (with default value : '')
     * TODO: Many compression mode
     */
    final public function prepare()
    {
        $this->basePath     = $this->kernelCacheDir . '/db/';
        $this->dataPath     = $this->basePath . static::DB_PATH . '/';

        $this->filesystem->mkdir($this->dataPath);
    }


    /**
     * Compress with format name like : hostname_2013_01_12-00_06_40.tar
     */
    final public function compression()
    {
        $fileName                       = $this->filePrefix . '_' . date('Y_m_d-H_i_s') . '.tar';
        $this->compressedArchivePath    = $this->basePath .'../dbcompressed/';

        $this->filesystem->mkdir($this->compressedArchivePath);
        $this->archivePath = $this->compressedArchivePath . $fileName;

        $archive = sprintf('tar -czf %s -C %s .', $this->archivePath, $this->basePath);

        $this->execute($archive);
    }


    /**
     * Handle process error on fails
     *
     * @param string $command
     *
     * @throws \RuntimeException
     */
    protected function execute($command)
    {
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }


    /**
     * Remove all dirs with files
     *
     */
    final public function cleanUp()
    {
        $this->filesystem->remove($this->basePath);
        $this->filesystem->remove($this->compressedArchivePath);
    }


    /**
     * Migration procedure for each databases type
     *
     * @return mixed
     */
    abstract public function dump();

    /**
     * Get command to execute dump
     *
     * @return string
     */
    abstract public function getCommand();

    /**
     * Return path of the archive
     *
     * @return mixed
     */
    public function getArchivePath()
    {
        return $this->archivePath;
    }


    /**
     * @param string $kernelCacheDir
     *
     * Setting Kernel cache directory
     */
    public function setKernelCacheDir($kernelCacheDir)
    {
        $this->kernelCacheDir = $kernelCacheDir;
    }

}
