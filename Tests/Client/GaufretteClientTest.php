<?php

namespace Dizda\CloudBackupBundle\Tests\Client;

use Dizda\CloudBackupBundle\Client\GaufretteClient;
use Gaufrette\File;
use Gaufrette\Filesystem;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;
// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class GaufretteClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function shouldDownloadAndSaveContentInANewFile()
    {
        $localFilesystemMock = $this->getMock(LocalFilesystem::class);
        $localFilesystemMock->expects($this->once())->method('dumpFile')
            ->with('/tmp/restore/db_2016-10-19.zip', 'foo bar');
        $client = new GaufretteClient('/tmp/restore/', $localFilesystemMock);
        $fileMock = $this
            ->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->setMethods(['getContent'])
            ->getMock()
        ;
        $fileMock->method('getContent')->willReturn('foo bar');
        $fileSystemMock = $this
            ->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(['keys', 'get'])
            ->getMock();
        $fileSystemMock->method('keys')->willReturn(['db_2016-10-19.zip']);
        $fileSystemMock->method('get')->with('db_2016-10-19.zip')->willReturn($fileMock);
        $client->addFilesystem($fileSystemMock);

        $file = $client->download();

        $this->assertInstanceOf('\SplFileInfo', $file);
        $this->assertEquals('/tmp/restore/db_2016-10-19.zip', $file->getPathname());
    }

    /**
     * @test
     * @expectedException \Dizda\CloudBackupBundle\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Parameter "$restoreFolder" is not set.
     */
    public function throwExceptionIfRestoreFolderIsNotConfigured()
    {
        $client = new GaufretteClient(null, $this->getMock(LocalFilesystem::class));
        $client->download();
    }
}
