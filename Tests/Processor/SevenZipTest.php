<?php

namespace Dizda\CloudBackupBundle\Tests\Processor;

use Dizda\CloudBackupBundle\Processor\SevenZipProcessor;

/**
 * Class SevenZipTest.
 */
 // backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class SevenZipTest extends \PHPUnit\Framework\TestCase
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
        $processor = new SevenZipProcessor(array());
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && 7z a  $archivePath"
        );

        // compress with password
        $processor = new SevenZipProcessor(array('password' => 'qwerty'));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && 7z a -p\"qwerty\" $archivePath"
        );

        // compress with compression rate = 0
        $processor = new SevenZipProcessor(array('compression_ratio' => 0));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && 7z a -mx0 $archivePath"
        );

        // compress with compression rate = 9
        $processor = new SevenZipProcessor(array('compression_ratio' => 9));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && 7z a -mx9 $archivePath"
        );

        // compress with compression rate = 2 - will be 1 - 7z don't support even numers of compression rate
        $processor = new SevenZipProcessor(array('compression_ratio' => 2));
        $this->assertEquals(
            $processor->getCompressionCommand($archivePath, $outputPath),
            "cd $outputPath && 7z a -mx1 $archivePath"
        );
    }
}
