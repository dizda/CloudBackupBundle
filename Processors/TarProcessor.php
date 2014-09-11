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
        if (isset($options['password']) && $options['password']) {
            $tarParams[] = '-P ' . $options['password'];
        }
        if (isset($options['compression_ratio']) && $options['compression_ratio']) {
            $compression_ratio = max(min($options['compression_ratio'], 9), 0);
            $zipParams[] = '-' . $compression_ratio;
        }
        return sprintf('tar %s cv %s | gzip %s > %s', implode(' ', $tarParams), $basePath, implode(' ', $zipParams), $archivePath);
    }
}
