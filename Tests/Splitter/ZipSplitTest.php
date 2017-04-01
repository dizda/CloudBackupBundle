<?php

namespace Dizda\CloudBackupBundle\Tests\Splitter;

use Dizda\CloudBackupBundle\Splitter\ZipSplitSplitter;

/**
 * @author Nick Doulgeridis
 */
// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class ZipSplitTest extends \PHPUnit\Framework\TestCase
{
	public function newGetMock($class){
		if(!class_exists('\PHPUnit\Framework\TestCase')){
			$this->getMock($class);
		}else{
			$this-getMockBuilder($class);
		}
	}
    public function testZipSplitCommand()
    {
        $file = '/var/backup/test.zip';
        $outputFolder = dirname($file);
        $split_size = 1000;
        $split = new ZipSplitSplitter($split_size);
        $split->setArchivePath($file);

        $this->assertEquals(
            $split->getCommand(),
            "zipsplit -n $split_size -b $outputFolder $file"
        );
    }
}
