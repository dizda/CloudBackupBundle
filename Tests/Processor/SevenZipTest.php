<?php

namespace Dizda\CloudBackupBundle\Tests\Processor;

use Dizda\CloudBackupBundle\Processor\SevenZipProcessor;
use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class SevenZipTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Processors
 */
class SevenZipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test different commands
     */
    public function testGetCompressionCommand()
    {
//        $processor = self::$kernel->getContainer()->get('dizda.cloudbackup.processor.7z');

        // build necessary data
        $rootPath = '/';
        $outputPath = '/var/backup/';
        $dateformat = 'Y-m-d_H-i-s';
        $processor = new SevenZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array()
        ));
        $archivePath = $outputPath . $processor->buildArchiveFilename();

        // compress with default params
        $processor = new SevenZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array()
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && 7z a  $archivePath");

        // compress with password
        $processor = new SevenZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('password' => 'qwerty')
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && 7z a -pqwerty $archivePath");

        // compress with compression rate = 0
        $processor = new SevenZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('compression_ratio' => 0)
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && 7z a -mx0 $archivePath");

        // compress with compression rate = 9
        $processor = new SevenZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('compression_ratio' => 9)
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && 7z a -mx9 $archivePath");

        // compress with compression rate = 2 - will be 1 - 7z don't support even numers of compression rate
        $processor = new SevenZipProcessor($rootPath, $outputPath, 'database', array(), array(
            'date_format' => $dateformat,
            'options'     => array('compression_ratio' => 2)
        ));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath), 
            "cd $outputPath && 7z a -mx1 $archivePath");
    }

}
