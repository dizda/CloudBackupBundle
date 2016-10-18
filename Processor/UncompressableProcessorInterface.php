<?php
namespace Dizda\CloudBackupBundle\Processor;

interface UncompressableProcessorInterface
{
   /**
     * Get uncompress command.
     *
     * @param string $basePath
     * @param string $fileName
     * @param string $uncompressPath
     *
     * @return string
     */
    public function getUncompressCommand($basePath, $fileName, $uncompressPath);
}
