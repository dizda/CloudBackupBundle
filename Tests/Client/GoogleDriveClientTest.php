<?php

namespace Dizda\CloudBackupBundle\Tests\Client;

use Dizda\CloudBackupBundle\Client\GoogleDriveClient;

/**
 * @author Tobias Nyholm
 */
// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class GoogleDriveClientTest extends \PHPUnit\Framework\TestCase
{
	public function newGetMock($class){
		if(class_exists('\PHPUnit_Framework_TestCase')){
			$this->getMock($class);
		}else{
			$this->getMockBuilder($class);
		}
	}
    public function testUpload()
    {
        $archive = '/biz/baz/boz';
        $mime = 'mime';
        $clientProvider = $this->newGetMock('Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider');
        $driveParent = $this->newGetMock('Google_Service_Drive_ParentReference');
        $driveService = $this->getDriveService();

        $client = $this->newGetMock('Google_Client')
            ->disableOriginalConstructor()
            ->setMethods(array('setDefer'))
            ->getMock();

        $client->expects($this->once())
            ->method('setDefer');

        $driveFile = $this->newGetMock('Google_Service_Drive_DriveFile');
        $driveFile->expects($this->once())
            ->method('setMimeType')
            ->with($this->equalTo($mime));

        $driveFile->expects($this->once())
            ->method('setParents')
            ->with($this->equalTo(array($driveParent)));

        $drive = $this->newGetMock('Dizda\CloudBackupBundle\Client\GoogleDriveClient')
            ->setConstructorArgs(array($clientProvider, 'foobar', '/foo/bar', '100'))
            ->setMethods(array('getClient', 'uploadFileInChunks', 'getMediaUploadFile', 'getDriveService', 'getDriveFile', 'getMimeType', 'getParentFolder'))
            ->getMock();

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
            ->method('getClient')
            ->willReturn($client);

        $drive->expects($this->once())
            ->method('getMediaUploadFile')
            ->with($this->equalTo($archive), $this->equalTo($client), $this->equalTo('request'), $this->equalTo($mime))
            ->willReturn('media');

        $drive->expects($this->once())
            ->method('uploadFileInChunks')
            ->with($this->equalTo($archive), $this->equalTo('media'))
            ->willReturn('media');

        $drive->upload($archive);
    }

    /**
     * @return mixed
     */
    private function getDriveService()
    {
        $driveFiles = $this->newGetMock('Google_Service_Drive_Files_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('insert'))
            ->getMock();
        $driveFiles->expects($this->once())
            ->method('insert')
            ->willReturn('request');

        $driveService = $this->newGetMock('Google_Service_Drive')
            ->disableOriginalConstructor()
            ->getMock();

        $driveService->files = $driveFiles;

        return $driveService;
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
}
