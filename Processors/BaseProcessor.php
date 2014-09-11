<?php

namespace Dizda\CloudBackupBundle\Processors;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

abstract class BaseProcessor
{
    protected $kernelCacheDir;
    protected $filePrefix;
    protected $folders;
    protected $format;
    protected $options;
    
    protected $filesystem;
    protected $basePath;
    protected $dataPath;
    protected $archivePath;
    protected $compressedArchivePath;
    
    /**
     * 
     * @param string $basePath Path to folder with archived files
     * @param string $filePrefix Prefix for archive file (e.g. sitename)
     * @param array  $folders Array of folders to archive (relative to site root folder)
     * @param string $dateformat Format for date function
     * @param array  $options Options from config
     */
    public function __construct($basePath, $filePrefix, $folders, $dateformat, $options)
    {
        $this->options = $options;
        
        $this->basePath   = $basePath;
        $this->filePrefix = $filePrefix;
        $this->folders    = $folders;
        $this->dateformat = $dateformat;
        
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->basePath);
    }

    /**
     * Make a copy of all folders specified in config
     */
    public function copyFolders(){
        // Copy folder for compression file
        $path = realpath($this->basePath.'../../../../');
        foreach($this->folders as $folder){
            $this->filesystem->mirror($path.$folder, $this->basePath.$folder);     
        }            
    }

    /**
     * Compress to file with name like : hostname_2013-01-12_00-06-40.tar
     */
    public function compress()
    {
        $fileName                    = $this->filePrefix . '_' . date($this->dateformat) . $this->getExtension();
        $this->compressedArchivePath = $this->basePath .'../backup_compressed/';

        $this->filesystem->mkdir($this->compressedArchivePath);
        $this->archivePath = $this->compressedArchivePath . $fileName;

        $archive = $this->getCompressionCommand($this->archivePath, $this->basePath);

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
    public function cleanUp()
    {
        $this->filesystem->remove($this->compressedArchivePath);
        $this->filesystem->remove($this->basePath);
    }

    /**
     * Return path of the archive
     *
     * @return string
     */
    public function getArchivePath()
    {
        return $this->archivePath;
    }
    
    /**
     * Get compression command
     * @param string $archivePath 
     * @param string $basePath
     * @return string
     */
    abstract public function getCompressionCommand($archivePath, $basePath);

    /**
     * Get file extention (with leading dot)
     * @return string
     */
    abstract public function getExtension();

}
