<?php

namespace Dizda\CloudBackupBundle\Splitters;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Finder\Finder;

/**
 * Interface SplittersInterface
 *
 * @package Dizda\CloudBackupBundle\Splitters
 * @author Nick Doulgeridis
 */
abstract class BaseSplitter
{
    protected $archivePath;
    protected $split_size;
    protected static $already_split = false;

    /**
     * @param $archive_path
     * @param $split_size
     */
    public function __construct($archive_path, $split_size)
    {
        $this->archivePath = $archive_path;
        $this->split_size = $split_size;
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
     * @return boolean
     */
    public static function getAlreadySplit()
    {
        return self::$already_split;
    }

    /**
     * @param boolean $already_split
     */
    public static function setAlreadySplit($already_split)
    {
        self::$already_split = $already_split;
    }

    /**
     * @void
     */
    public abstract function split();

    /**
     * @return string
     */
    public abstract function getCommand();
}