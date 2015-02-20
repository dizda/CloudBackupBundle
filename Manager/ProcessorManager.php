<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Processor\ProcessorInterface;
use Dizda\CloudBackupBundle\Splitter\ZipSplitSplitter;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * A ProcessorManager handles the compression, cleanup etc for a specific processor.
 *
 * @author Tobias Nyholm
 */
class ProcessorManager
{
    /**
     * @var \Dizda\CloudBackupBundle\Processor\ProcessorInterface processor
     */
    protected $processor;

    /**
     * This is the path to the latest created archive.
     *
     * @var string archivePath
     */
    protected $archivePath;

    /**
     * @var array folders
     */
    protected $folders;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem filesystem
     */
    protected $filesystem;

    /**
     * @var string rootPath
     */
    protected $rootPath;

    /**
     * @var string outputPath
     */
    protected $outputPath;

    /**
     * @var string compressedArchivePath
     */
    protected $compressedArchivePath;

    /**
     * @var string filePrefix
     */
    protected $filePrefix;

    /**
     * @var string dateFormat
     */
    protected $dateFormat;

    /**
     * @param string $rootPath   Path to root folder
     * @param string $outputPath Path to folder with archived files
     * @param string $filePrefix Prefix for archive file (e.g. sitename)
     * @param string $dateFormat Date function format
     * @param array  $folders    Array of folders to archive (relative to $rootPath)
     */
    public function __construct($rootPath, $outputPath, $filePrefix, $dateFormat, array $folders = array())
    {
        $this->rootPath   = $rootPath;
        $this->outputPath = $outputPath;
        $this->filePrefix = $filePrefix;
        $this->folders    = $folders;
        $this->dateFormat = $dateFormat;
        $this->compressedArchivePath = $this->outputPath.'../backup_compressed/';

        $this->filesystem = new Filesystem();
    }

    /**
     * @param \Dizda\CloudBackupBundle\Processor\ProcessorInterface $processor
     *
     * @return $this
     */
    public function setProcessor(ProcessorInterface $processor)
    {
        $this->processor = $processor;

        return $this;
    }

    /**
     * Make a copy of all folders specified in config.
     */
    public function copyFolders()
    {
        // Copy folder for compression file
        foreach ($this->folders as $folder) {
            $this->filesystem->mirror($this->rootPath.'/'.$folder, $this->outputPath.'folders/'.$folder);
        }
    }

    /**
     * Compress to file with name like : hostname_2013-01-12_00-06-40.tar.
     */
    public function compress()
    {
        $this->archivePath = $this->compressedArchivePath.$this->buildArchiveFilename();

        $archive = $this->processor->getCompressionCommand($this->archivePath, $this->outputPath);

        $this->filesystem->mkdir($this->compressedArchivePath);
        $this->filesystem->mkdir($this->outputPath);
        $this->execute($archive);

        $this->split();
    }

    /**
     * Return archive file name.
     *
     * @return string
     */
    public function buildArchiveFilename()
    {
        return $this->filePrefix.'_'.date($this->dateFormat).$this->processor->getExtension();
    }

    /**
     * Handle process error on fails.
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
     * Return path of the archive.
     *
     * @return string
     */
    public function getArchivePath()
    {
        return $this->archivePath;
    }

    /**
     * Remove all dirs with files.
     */
    public function cleanUp()
    {
        $this->filesystem->remove($this->compressedArchivePath);
        $this->filesystem->remove($this->outputPath);
    }

    /**
     * Here is the split.
     */
    private function split()
    {
        var_dump('----split');
        $split = new ZipSplitSplitter($this->archivePath, 350000);
        $split->executeSplit();
        $splitFiles = $split->getSplitFiles();
        var_dump($splitFiles);
        var_dump('----split');
    }

    public function getName()
    {
        return $this->processor->getName();
    }
}
