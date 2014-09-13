<?php

namespace Dizda\CloudBackupBundle\Tests\Processors;

use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class TarTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Processors
 */
class TarTest extends AbstractTesting
{
    /**
     * Test different commands
     */
    public function testGetCompressionCommand()
    {
        $processor = self::$kernel->getContainer()->get('dizda.cloudbackup.processor.tar');

        // build necessary data
        $basePath = '/var/backup/';
        $dateformat = 'Y-m-d_H-i-s';
        $processor->__construct($basePath, 'database', array(), $dateformat, array());
        $archivePath = $basePath . $processor->buildArchiveFilename();

        // compress with default params
        $processor->__construct($basePath, 'database', array(), $dateformat, array());
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "tar  c -C $basePath . | gzip  > $archivePath");

        // compress with password - password not used in tar processor
        $processor->__construct($basePath, 'database', array(), $dateformat, array('password' => 'qwerty'));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "tar  c -C $basePath . | gzip  > $archivePath");

        // compress with compression rate = 0
        $processor->__construct($basePath, 'database', array(), $dateformat, array('compression_ratio' => 0));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "tar  c -C $basePath . | gzip -0 > $archivePath");

        // compress with compression rate = 9
        $processor->__construct($basePath, 'database', array(), $dateformat, array('compression_ratio' => 9));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $basePath), 
            "tar  c -C $basePath . | gzip -9 > $archivePath");
    }

}
