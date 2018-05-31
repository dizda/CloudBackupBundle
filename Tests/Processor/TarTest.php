<?php

namespace Dizda\CloudBackupBundle\Tests\Processor;

use Dizda\CloudBackupBundle\Processor\TarProcessor;
use Symfony\Component\Process\ProcessUtils;

/**
 * Class TarTest.
 */
// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class TarTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test different commands.
     */
    public function testGetCompressionCommand()
    {
        // build necessary data
        $outputPath  = '/var/backup/';
        $archivePath = $outputPath . 'coucou.zip';

        // compress with default params
        $processor = new TarProcessor(array());
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "tar  c -C ". ProcessUtils::escapeArgument($outputPath)." . | gzip  > ". ProcessUtils::escapeArgument($archivePath)
        );

        // compress with password - password not used in tar processor
        $processor = new TarProcessor(array('password' => 'qwerty'));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "tar  c -C ". ProcessUtils::escapeArgument($outputPath)." . | gzip  > ". ProcessUtils::escapeArgument($archivePath)
        );

        // compress with compression rate = 0
        $processor = new TarProcessor(array('compression_ratio' => 0));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "tar  c -C ". ProcessUtils::escapeArgument($outputPath)." . | gzip -0 > ". ProcessUtils::escapeArgument($archivePath)
        );

        // compress with compression rate = 9
        $processor = new TarProcessor(array('compression_ratio' => 9));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "tar  c -C ". ProcessUtils::escapeArgument($outputPath)." . | gzip -9 > ". ProcessUtils::escapeArgument($archivePath)
        );
    }
}
