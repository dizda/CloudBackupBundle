<?php

namespace Dizda\CloudBackupBundle\Splitters;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Process;

/**
 * Class ZipSplitSplitter
 *
 * @package Dizda\CloudBackupBundle\Splitters
 * @author Nick Doulgeridis
 */
class ZipSplitSplitter extends BaseSplitter
{
    /**
     * Runs the zipsplit command
     */
    public function executeSplit()
    {
        $command = $this->getCommand();
        $process = new Process($command);
        $process->setTimeout(null);
        $process->run();
        $this->renameSplitFiles();
    }

    /**
     * Get the zipsplit command
     */
    public function getCommand()
    {
        return sprintf("zipsplit -n %s -b %s %s",
            $this->getSplitSize(),
            $this->getOutputFolder(),
            $this->getArchivePath());
    }

    /**
     * Rename files we split using the naming convention in config
     */
    private function renameSplitFiles()
    {
        $c = 1;
        $wholeFile = new File($this->getArchivePath());
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