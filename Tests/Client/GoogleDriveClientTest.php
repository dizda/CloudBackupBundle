<?php

namespace Dizda\CloudBackupBundle\Tests\Client;

use Dizda\CloudBackupBundle\Client\GoogleDriveClient;

/**
 * @author Tobias Nyholm
 */
class GoogleDriveClientTest extends \PHPUnit_Framework_TestCase
{
    public function testUpload()
    {
        $archive = '/biz/baz/boz';
        $mime = 'mime';
        $clientProvider = $this->getMock('Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider');
        $driveParent = $this->getMock('Google_Service_Drive_ParentReference');
        $driveService = $this->getDriveService();

        $driveFile = $this->getMock('Google_Service_Drive_DriveFile');
        $driveFile->expects($this->once())
            ->method('setMimeType')
            ->with($this->equalTo($mime));

        $driveFile->expects($this->once())
            ->method('setParents')
            ->with($this->equalTo(array($driveParent)));

        $drive = $this->getMockBuilder('Dizda\CloudBackupBundle\Client\GoogleDriveClient')
            ->setConstructorArgs(array($clientProvider, 'foobar', '/foo/bar'))
            ->setMethods(array('output', 'getDriveService', 'getDriveFile', 'getMimeType', 'getParentFolder', 'getFileContents'))
            ->getMock();

        $drive->expects($this->any())
            ->method('output');

        $drive->expects($this->once())
            ->method('getDriveService')
            ->willReturn($driveService);

        $drive->expects($this->once())
            ->method('getDriveFile')
            ->with($this->equalTo($archive))
            ->willReturn($driveFile);

        $drive->expects($this->once())
            ->method('getMimeType')
            ->with($this->equalTo($archive))
            ->willReturn($mime);

        $drive->expects($this->once())
            ->method('getParentFolder')
            ->with($this->equalTo($driveService))
            ->willReturn($driveParent);

        $drive->expects($this->once())
            ->method('getFileContents')
            ->with($this->equalTo($archive))
            ->willReturn('data');

        $drive->upload($archive);
    }

    /**
     * @return mixed
     */
    private function getDriveService()
    {
        $driveFiles = $this->getMockBuilder('Google_Service_Drive_Files_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('insert'))
            ->getMock();
        $driveFiles->expects($this->once())
            ->method('insert');

        $driveService = $this->getMockBuilder('Google_Service_Drive')
            ->disableOriginalConstructor()
            ->getMock();

        $driveService->files = $driveFiles;

        return $driveService;
    }

    /**
     * @expectedException \Exception
     */
    public function testGetFileContents()
    {
        $drive = new Dummy();
        $drive->getFileContents('/example/path');
    }

    public function testGetDriveFile()
    {
        $drive = new Dummy();
        $file = $drive->getDriveFile('foo/bar.txt');
        $this->assertEquals('bar.txt', $file->getTitle());
    }
}

class Dummy extends GoogleDriveClient
{
    public function __construct($path = '/foo/bar/biz')
    {
        $this->remotePath = $path;
    }

    public function getMimeType($a)
    {
        return parent::getMimeType($a);
    }
    public function getParentFolder(\Google_Service_Drive $a)
    {
        return parent::getParentFolder($a);
    }
    public function getDriveService()
    {
        return parent::getDriveService();
    }
    public function getDriveFile($a)
    {
        return parent::getDriveFile($a);
    }
    public function output($a, $b = true)
    {
        parent::output($a, $b);
    }
    public function getFileContents($a)
    {
        return parent::getFileContents($a);
    }
}
