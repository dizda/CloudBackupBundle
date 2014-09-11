<?php

namespace Dizda\CloudBackupBundle\Processors;

class TarProcessor extends BaseProcessor
{
    public function getExtension() {
        return '.tar';
    }
    
    public function getCompressionCommand($archivePath, $basePath)
    {
        $tarParams = array();
        $zipParams = array();
        if (isset($this->options['compression_ratio']) && $this->options['compression_ratio']) {
            $compression_ratio = max(min($this->options['compression_ratio'], 9), 0);
            $zipParams[] = '-' . $compression_ratio;
        }
        return sprintf('tar %s c -C %s . | gzip %s > %s', 
            implode(' ', $tarParams), 
            $basePath, 
            implode(' ', $zipParams), 
            $archivePath);
    }
}
