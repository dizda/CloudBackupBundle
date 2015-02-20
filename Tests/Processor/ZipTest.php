<?php

namespace Dizda\CloudBackupBundle\Tests\Processor;

use Dizda\CloudBackupBundle\Processor\ZipProcessor;
use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class ZipTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Processors
 */
class ZipTest extends \PHPUnit_Framework_TestCase
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
        $processor = new ZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array()
        ));
        $archivePath = $outputPath . $processor->buildArchiveFilename();

        // compress with default params
        $processor = new ZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array()
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && zip -r $archivePath .");

        // compress with password
        $processor = new ZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('password' => 'qwerty')
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && zip -r -P qwerty $archivePath .");

        // compress with compression rate = 0
        $processor = new ZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('compression_ratio' => 0)
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && zip -r -0 $archivePath .");

        // compress with compression rate = 9
        $processor = new ZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('compression_ratio' => 9)
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && zip -r -9 $archivePath .");
    }

}
