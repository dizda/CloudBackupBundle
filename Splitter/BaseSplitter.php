<?php

namespace Dizda\CloudBackupBundle\Splitter;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Finder\Finder;

/**
 * Interface SplittersInterface
 *
 * @package Dizda\CloudBackupBundle\Splitter
 * @author Nick Doulgeridis
 */
abstract class BaseSplitter
{
    /**
     * @var string archivePath
     */
    private $archivePath;

    /**
     * @var integer splitSize
     */
    private $splitSize;

    /**
     * @param $archive_path
     * @param $split_size
     */
    public function __construct($archive_path, $split_size)
    {
        $this->archivePath = $archive_path;
        $this->splitSize = $split_size;
    }

    /**
     * @return array
     */
    public function getSplitFiles()
    {
        $file = new File($this->archivePath);
        $finder = new Finder();
        $finder->files()->in(dirname($this->archivePath))->notName($file->getFilename())->sortByModifiedTime();

        return iterator_to_array($finder);
    }

    /**
     * @return integer
     */
    public function getSplitSize()
    {
        return $this->splitSize;
    }

    /**
     * @return string
     */
    public function getOutputFolder()
    {
        return dirname($this->archivePath);
    }

    /**
     * @return string
     */
    public function getArchivePath()
    {
        return $this->archivePath;
    }

    /**
     * @void
     */
    public abstract function executeSplit();

    /**
     * @return string
     */
    public abstract function getCommand();
}