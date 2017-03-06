<?php

namespace Dizda\CloudBackupBundle\Processor;

class ZipProcessor extends BaseProcessor implements ProcessorInterface, UncompressableProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return '.zip';
    }

    /**
     * {@inheritdoc}
     */
    public function getCompressionCommand($archivePath, $basePath)
    {
        $params = array('-r');

        if (isset($this->options['password']) && $this->options['password']) {
            $params[] = '-P "'.$this->options['password'].'"';
        }

        if (isset($this->options['compression_ratio']) && $this->options['compression_ratio'] >= 0) {
            $compression_ratio = max(min($this->options['compression_ratio'], 9), 0);
            $params[] = '-'.$compression_ratio;
        }

        $binaryFile = escapeshellarg(isset($this->options['executable']) ? $this->options['executable'] : 'zip');

        return sprintf('cd %s && %s %s %s .', $basePath, $binaryFile, implode(' ', $params), $archivePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getUncompressCommand($basePath, $fileName, $uncompressPath)
    {
        return sprintf('cd %s && unzip -o %s -d %s', $basePath, $fileName, $uncompressPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Zip';
    }
}
