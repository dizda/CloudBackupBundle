<?php

namespace Dizda\CloudBackupBundle\Processors;

use Dizda\CloudBackupBundle\Processors\ProcessorInterface;
use Dizda\CloudBackupBundle\Splitters\ZipSplitSplitter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

abstract class BaseProcessor implements ProcessorInterface
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
     * @param array  $processor Date function format, and processor options
     */
    public function __construct($rootPath, $outputPath, $filePrefix, $folders, $processor)
    {
        $this->options = $processor['options'];
        
        $this->rootPath   = $rootPath;
        $this->outputPath = $outputPath;
        $this->filePrefix = $filePrefix;
        $this->folders    = $folders;
        $this->dateformat = $processor['date_format'];
        
        $this->filesystem = new Filesystem();
    }

    /**
     * Make a copy of all folders specified in config
     */
    public function copyFolders(){
        // Copy folder for compression file
        foreach($this->folders as $folder){
            $this->filesystem->mirror($this->rootPath . '/' . $folder, $this->outputPath . 'folders/' . $folder);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function compress()
    {
        $this->compressedArchivePath = $this->outputPath .'../backup_compressed/';
        $this->archivePath = $this->compressedArchivePath . $this->buildArchiveFilename();

        $archive = $this->getCompressionCommand($this->archivePath, $this->outputPath);
        
        $this->filesystem->mkdir($this->compressedArchivePath);
        $this->filesystem->mkdir($this->outputPath);
        $this->execute($archive);



        // Below is split
        var_dump('----split');
        $split = new ZipSplitSplitter($this->archivePath, 350000);
        $split->executeSplit();
        $splitFiles = $split->getSplitFiles();
        var_dump($splitFiles);
        var_dump('----split');
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
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getArchivePath()
    {
        return $this->archivePath;
    }
}
