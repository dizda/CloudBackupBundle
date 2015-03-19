<?php

namespace Dizda\CloudBackupBundle\Splitter;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Process\Process;

/**
 * Class ZipSplitSplitter.
 *
 * @author Nick Doulgeridis
 */
class ZipSplitSplitter extends BaseSplitter
{
    /**
     * Runs the zipsplit command.
     */
    public function executeSplit()
    {
        $command = $this->getCommand();
        $process = new Process($command);
        $process->setTimeout(null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \Exception($process->getOutput(), 1);
        }

        $this->renameSplitFiles();
    }

    /**
     * Get the zipsplit command.
     */
    public function getCommand()
    {
        return sprintf("zipsplit -n %s -b %s %s",
            $this->getSplitSize(),
            $this->getOutputFolder(),
            $this->getArchivePath()
        );
    }

    /**
     * Rename files we split using the naming convention in config.
     */
    private function renameSplitFiles()
    {
        $c = 1;
        $wholeFile = new File($this->getArchivePath());

        foreach ($this->getSplitFiles() as $file/* @var $file SplFileInfo */) {
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
