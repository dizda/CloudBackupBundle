<?php
namespace Dizda\CloudBackupBundle\Processors;

/**
 * Interface ProcessorInterface
 *
 * @package Dizda\CloudBackupBundle\Processors
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
interface ProcessorInterface
{
    /**
     * Get compression command
     *
     * @param string $archivePath
     * @param string $basePath
     *
     * @return string
     */
    public function getCompressionCommand($archivePath, $basePath);

    /**
     * Get file extention (with leading dot)
     *
     * @return string
     */
    public function getExtension();
}