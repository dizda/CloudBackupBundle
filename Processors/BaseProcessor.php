<?php

namespace Dizda\CloudBackupBundle\Processors;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

abstract class BaseProcessor
{
    protected $filePrefix;
    protected $folders;
    protected $format;
    protected $options;
    
    protected $rootPath;
    protected $outputPath;
    protected $dataPath;
    protected $archivePath;
    protected $compressedArchivePath;
    
    protected $filesystem;
    
    /**
     * 
     * @param string $rootPath Path to root folder
     * @param string $outputPath Path to folder with archived files
     * @param string $filePrefix Prefix for archive file (e.g. sitename)
     * @param array  $folders Array of folders to archive (relative to $rootPath)
     * @param string $dateformat Format for date function
     * @param array  $options Options from config
     */
    public function __construct($rootPath, $outputPath, $filePrefix, $folders, $dateformat, $options)
    {
        $this->options = $options;
        
        $this->rootPath   = $rootPath;
        $this->outputPath = $outputPath;
        $this->filePrefix = $filePrefix;
        $this->folders    = $folders;
        $this->dateformat = $dateformat;
        
        $this->filesystem = new Filesystem();
    }

    /**
     * Make a copy of all folders specified in config
     */
    public function copyFolders(){
        // Copy folder for compression file
        foreach($this->folders as $folder){
            $this->filesystem->mirror($this->rootPath.'/'.$folder, $this->outputPath.$folder);
        }
    }

    /**
     * Compress to file with name like : hostname_2013-01-12_00-06-40.tar
     */
    public function compress()
    {
        $this->compressedArchivePath = $this->outputPath .'../backup_compressed/';
        $this->archivePath = $this->compressedArchivePath . $this->buildArchiveFilename();

        $archive = $this->getCompressionCommand($this->archivePath, $this->outputPath);
        
        $this->filesystem->mkdir($this->compressedArchivePath);
        $this->filesystem->mkdir($this->outputPath);
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
        $this->filesystem->remove($this->outputPath);
    }

    /**
     * Return archive file name
     *
     * @return string
     */
    public function buildArchiveFilename()
    {
        return $this->filePrefix . '_' . date($this->dateformat) . $this->getExtension();
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
