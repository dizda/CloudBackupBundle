<?php
namespace Dizda\CloudBackupBundle\Processor;

/**
 * Interface ProcessorInterface.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
interface ProcessorInterface
{
    /**
     * Get compression command.
     *
     * @param string $archivePath
     * @param string $basePath
     *
     * @return string
     */
    public function getCompressionCommand($archivePath, $basePath);

    /**
     * Get file extension (with leading dot).
     *
     * @return string
     */
    public function getExtension();

    /**
     * The name of the processor.
     *
     * @return string
     */
    public function getName();
}
