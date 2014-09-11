<?php

namespace Dizda\CloudBackupBundle\Processors;

class SevenZipProcessor extends BaseProcessor
{
    public function getExtension() {
        return '.7z';
    }
    
    public function getCompressionCommand($archivePath, $basePath)
    {
        $params = array();
        if (isset($options['password']) && $options['password']) {
            $params[] = '-p' . $options['password'];
        }
        if (isset($options['compression_ratio']) && $options['compression_ratio']) {
            $compression_ratio = max(min($options['compression_ratio'], 9), 0);
            if ($compression_ratio > 1 && ($compression_ratio % 2 == 0)) {
                $compression_ratio--;
            }
            $params[] = '-mx' . $compression_ratio;
        }
        return sprintf('cd %s && 7z a %s %s', $basePath, implode(' ', $params), $archivePath);
    }
}
