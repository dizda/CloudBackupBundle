<?php

namespace Dizda\CloudBackupBundle\Tests\Processors;

use Dizda\CloudBackupBundle\Processors\TarProcessor;
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
        // build necessary data
        $rootPath = '/';
        $outputPath = '/var/backup/';
        $dateformat = 'Y-m-d_H-i-s';
        $processor = new TarProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array()
        ));
        $archivePath = $outputPath . $processor->buildArchiveFilename();

        // compress with default params
        $processor = new TarProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array()
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "tar  c -C $outputPath . | gzip  > $archivePath");

        // compress with password - password not used in tar processor
        $processor = new TarProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('password' => 'qwerty')
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "tar  c -C $outputPath . | gzip  > $archivePath");

        // compress with compression rate = 0
        $processor = new TarProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('compression_ratio' => 0)
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "tar  c -C $outputPath . | gzip -0 > $archivePath");

        // compress with compression rate = 9
        $processor = new TarProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('compression_ratio' => 9)
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "tar  c -C $outputPath . | gzip -9 > $archivePath");
    }

}
