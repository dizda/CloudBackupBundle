<?php

namespace Dizda\CloudBackupBundle\Processor;

class SevenZipProcessor extends BaseProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return '.7z';
    }

    /**
     * {@inheritdoc}
     */
    public function getCompressionCommand($archivePath, $basePath)
    {
        $params = array();

        if (isset($this->options['password']) && $this->options['password']) {
            $params[] = '-p"'.$this->options['password'].'"';
        }

        if (isset($this->options['compression_ratio']) && $this->options['compression_ratio'] >= 0) {
            $compression_ratio = max(min($this->options['compression_ratio'], 9), 0);

            if ($compression_ratio > 1 && ($compression_ratio % 2 == 0)) {
                $compression_ratio--;
            }

            $params[] = '-mx'.$compression_ratio;
        }

        return sprintf('cd %s && 7z a %s %s', $basePath, implode(' ', $params), $archivePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'SevenZip';
    }
}
