<?php

namespace Dizda\CloudBackupBundle\Processors;

class ZipProcessor extends BaseProcessor
{
    
    public function getExtension() {
        return '.zip';
    }
    
    public function getCompressionCommand($archivePath, $basePath)
    {
        $params = array('-r');
        if (isset($this->options['password']) && $this->options['password']) {
            $params[] = '-P ' . $this->options['password'];
        }
        if (isset($this->options['compression_ratio']) && $this->options['compression_ratio']) {
            $compression_ratio = max(min($this->options['compression_ratio'], 9), 0);
            $params[] = '-' . $compression_ratio;
        }
        return sprintf('cd %s && zip %s %s .', $basePath, implode(' ', $params), $archivePath);
    }
}
