<?php

namespace Dizda\CloudBackupBundle\Tests\Processor;

use Dizda\CloudBackupBundle\Processor\ZipProcessor;

/**
 * Class ZipTest.
 */
 // backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class ZipTest extends \PHPUnit\Framework\TestCase
{ 
    /**
     * Compatibility for older PHPUnit versions
     *
     * @param string $originalClassName
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMock($originalClassName) {
        if(is_callable(array('parent', 'createMock'))) {
            return parent::createMock($originalClassName);
        } else {
            return $this->getMock($originalClassName);
        }
    }
    /**
     * Test different commands.
     */
    public function testGetCompressionCommand()
    {
        // build necessary data
        $outputPath  = '/var/backup/';
        $archivePath = $outputPath . 'coucou.zip';

        // compress with default params
        $processor = new ZipProcessor(array());
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && zip -r $archivePath ."
        );

        // compress with password
        $processor = new ZipProcessor(array('password' => 'qwerty'));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && zip -r -P \"qwerty\" $archivePath ."
        );

        // compress with compression rate = 0
        $processor = new ZipProcessor(array('compression_ratio' => 0));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && zip -r -0 $archivePath ."
        );

        // compress with compression rate = 9
        $processor = new ZipProcessor(array('compression_ratio' => 9));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && zip -r -9 $archivePath ."
        );
    }
}
