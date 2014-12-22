<?php

namespace Dizda\CloudBackupBundle\Splitters;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Process;

/**
 * @author Nick Doulgeridis
 */
class ZipSplitSplitter
{
    private $archivePath;
    private $split_size;
    private static $already_split = false;

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
     * Runs the zipsplit command
     */
    public function split()
    {
        if(!self::$already_split)
        {
            $command = $this->getCommand();
            $process = new Process($command);
            $process->setTimeout(null);
            $process->run();

            $this->renameSplitFiles();
            self::setAlreadySplit(true);
        }
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
     * Get the zipsplit command
     */
    public function getCommand()
    {
        return sprintf("zipsplit -n %s -b %s %s",
            $this->split_size,
            dirname($this->archivePath),
            $this->archivePath);
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

    private function renameSplitFiles()
    {
        $c = 1;
        $wholeFile = new File($this->archivePath);
        foreach ($this->getSplitFiles() as $file/* @var $file SplFileInfo */)
        {
            $new_filename = sprintf("%s/%spart%s.%s",
                $wholeFile->getPath(),
                basename($wholeFile->getFilename(), $wholeFile->getExtension()),
                $c++,
                $wholeFile->getExtension()
            );
            rename($file->getRealPath(), $new_filename);
        }

    }


}