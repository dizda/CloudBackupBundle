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
     * Compress to file with name like : hostname_2013-01-12_00-06-40.tar
     */
    public function compress();

    /**
     * Remove all dirs with files
     */
    public function cleanUp();

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

    /**
     * Return path of the archive
     *
     * @return string
     */
    public function getArchivePath();
}