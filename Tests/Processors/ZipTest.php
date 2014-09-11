<?php

namespace Dizda\CloudBackupBundle\Tests\Processors;

use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class ZipTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Processors
 */
class ZipTest extends AbstractTesting
{
    /**
     * Test different commands
     */
    public function testGetCompressionCommand()
    {
        $processor = self::$kernel->getContainer()->get('dizda.cloudbackup.processor.zip');

        // build necessary data
        $basePath = '/var/backup/';
        $dateformat = 'Y-m-d_H-i-s';
        $processor->__construct($basePath, 'database', array(), $dateformat, array());
        $archivePath = $basePath . $processor->buildArchiveFilename();

        // compress with default params
        $processor->__construct($basePath, 'database', array(), $dateformat, array());
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && zip -r $archivePath .");

        // compress with password
        $processor->__construct($basePath, 'database', array(), $dateformat, array('password' => 'qwerty'));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && zip -r -P qwerty $archivePath .");

        // compress with compression rate = 0
        $processor->__construct($basePath, 'database', array(), $dateformat, array('compression_ratio' => 0));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && zip -r -0 $archivePath .");

        // compress with compression rate = 9
        $processor->__construct($basePath, 'database', array(), $dateformat, array('compression_ratio' => 9));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && zip -r -9 $archivePath .");
    }

}
