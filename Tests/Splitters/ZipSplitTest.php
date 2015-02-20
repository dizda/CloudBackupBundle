<?php

namespace Dizda\CloudBackupBundle\Tests\Splitters;

use Dizda\CloudBackupBundle\Splitter\ZipSplitSplitter;

/**
 * @author Nick Doulgeridis
 */
class ZipSplitTest extends \PHPUnit_Framework_TestCase
{
    public function testZipSplitCommand()
    {
        $file = '/var/backup/test.zip';
        $outputFolder = dirname($file);
        $split_size = 1000;
        $split = new ZipSplitSplitter($file, $split_size);

        $this->assertEquals(
            $split->getCommand(),
            "zipsplit -n $split_size -b $outputFolder $file"
        );
    }
}

