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
        if (isset($options['password']) && $options['password']) {
            $params[] = '-P ' . $options['password'];
        }
        if (isset($options['compression_ratio']) && $options['compression_ratio']) {
            $compression_ratio = max(min($options['compression_ratio'], 9), 0);
            $params[] = '-' . $compression_ratio;
        }
        return sprintf('cd %s && zip %s %s .', $basePath, implode(' ', $params), $archivePath);
    }
}
