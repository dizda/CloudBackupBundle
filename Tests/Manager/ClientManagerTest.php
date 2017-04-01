<?php
namespace Dizda\CloudBackupBundle\Tests\Manager;

use Dizda\CloudBackupBundle\Client\ClientInterface;
use Dizda\CloudBackupBundle\Client\DownloadableClientInterface;
use Dizda\CloudBackupBundle\Manager\ClientManager;
use Psr\Log\LoggerInterface;

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class ClientManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function shouldExecuteDownloadForFirstDownloadableClient()
    {
        $clients = [];
        $clients[] = $this->createMock(ClientInterface::class);
        $clientMock = $this->createMock(DownloadableClientInterface::class);
        $fileMock = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->setConstructorArgs([tempnam(sys_get_temp_dir(), '')])
            ->getMock();
        $clientMock->expects($this->once())->method('download')->willReturn($fileMock);
        $clients[] = $clientMock;

        $clientManager = new ClientManager($this->createMock(LoggerInterface::class), $clients);
        $this->assertSame($fileMock, $clientManager->download());
    }

    /**
     * @test
     * @expectedException \Dizda\CloudBackupBundle\Exception\MissingDownloadableClientsException
     * @expectedExceptionMessage No downloadable client is registered.
     */
    public function shouldThrowExceptionIfNoChildIsADownloadableClient()
    {
        $clients = [];
        $clients[] = $this->createMock(ClientInterface::class);
        $clients[] = $this->createMock(ClientInterface::class);

        $clientManager = new ClientManager($this->createMock(LoggerInterface::class), $clients);
        $clientManager->download();
    }
}
