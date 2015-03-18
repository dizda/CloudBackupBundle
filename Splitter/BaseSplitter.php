<?php

namespace Dizda\CloudBackupBundle\Splitter;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Finder\Finder;

/**
 * Interface SplittersInterface.
 *
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
     * @param $splitSize
     */
    public function __construct($splitSize)
    {
        $this->splitSize = $splitSize;
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
     * @param string $archivePath
     *
     * @return $this
     */
    public function setArchivePath($archivePath)
    {
        $this->archivePath = $archivePath;

        return $this;
    }

    /**
     * @void
     */
    abstract public function executeSplit();

    /**
     * @return string
     */
    abstract public function getCommand();
}
