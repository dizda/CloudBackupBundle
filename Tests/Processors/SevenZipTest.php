<?php

namespace Dizda\CloudBackupBundle\Tests\Processors;

use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class SevenZipTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Processors
 */
class SevenZipTest extends AbstractTesting
{
    /**
     * Test different commands
     */
    public function testGetCompressionCommand()
    {
        $processor = self::$kernel->getContainer()->get('dizda.cloudbackup.processor.7z');

        // build necessary data
        $basePath = '/var/backup/';
        $dateformat = 'Y-m-d_H-i-s';
        $processor->__construct($basePath, 'database', array(), $dateformat, array());
        $archivePath = $basePath . $processor->buildArchiveFilename();

        // compress with default params
        $processor->__construct($basePath, 'database', array(), $dateformat, array());
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && 7z a  $archivePath");

        // compress with password
        $processor->__construct($basePath, 'database', array(), $dateformat, array('password' => 'qwerty'));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && 7z a -pqwerty $archivePath");

        // compress with compression rate = 0
        $processor->__construct($basePath, 'database', array(), $dateformat, array('compression_ratio' => 0));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && 7z a -mx0 $archivePath");

        // compress with compression rate = 9
        $processor->__construct($basePath, 'database', array(), $dateformat, array('compression_ratio' => 9));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && 7z a -mx9 $archivePath");

        // compress with compression rate = 2 - will be 1 - 7z don't support even numers of compression rate
        $processor->__construct($basePath, 'database', array(), $dateformat, array('compression_ratio' => 2));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "cd $basePath && 7z a -mx1 $archivePath");
    }

}
