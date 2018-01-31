<?php
namespace Dizda\CloudBackupBundle\Tests\Manager;


use Dizda\CloudBackupBundle\Event\RestoreEvent;
use Dizda\CloudBackupBundle\Event\RestoreFailedEvent;
use Dizda\CloudBackupBundle\Manager\ClientManager;
use Dizda\CloudBackupBundle\Manager\DatabaseManager;
use Dizda\CloudBackupBundle\Manager\ProcessorManager;
use Dizda\CloudBackupBundle\Manager\RestoreManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

class RestoreManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function shouldRestoreDatabase()
    {
        $fileMock = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->setConstructorArgs([tempnam(sys_get_temp_dir(), '')])
            ->getMock();

        $databaseManagerMock = $this->getMockBuilder(DatabaseManager::class)->disableOriginalConstructor()->getMock();
        $databaseManagerMock->expects($this->once())->method('restore');
        $clientManagerMock = $this->getMockBuilder(ClientManager::class)->disableOriginalConstructor()->getMock();
        $clientManagerMock->expects($this->once())->method('download')->willReturn($fileMock);
        $processorManagerMock = $this->getMockBuilder(ProcessorManager::class)->disableOriginalConstructor()->getMock();
        $processorManagerMock->expects($this->once())->method('uncompress');
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects($this->once())->method('dispatch')->with(RestoreEvent::RESTORE_COMPLETED);
        $filesystemMock = $this->createMock(Filesystem::class);

        $restoreManager = new RestoreManager(
            $databaseManagerMock,
            $clientManagerMock,
            $processorManagerMock,
            $eventDispatcherMock,
            '',
            $filesystemMock,
            true
        );

        $restoreManager->execute();
    }

    /**
     * @test
     * @expectedException \Dizda\CloudBackupBundle\Exception\RestoringNotAvailableException
     * @expectedExceptionMessage Restoring is not available.
     */
    public function shouldNotRestoreDatabase()
    {
        $databaseManagerMock = $this->getMockBuilder(DatabaseManager::class)->disableOriginalConstructor()->getMock();
        $databaseManagerMock->expects($this->never())->method('restore');
        $clientManagerMock = $this->getMockBuilder(ClientManager::class)->disableOriginalConstructor()->getMock();
        $clientManagerMock->expects($this->never())->method('download');
        $processorManagerMock = $this->getMockBuilder(ProcessorManager::class)->disableOriginalConstructor()->getMock();
        $processorManagerMock->expects($this->never())->method('uncompress');
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects($this->never())->method('dispatch');
        $filesystemMock = $this->createMock(Filesystem::class);

        $restoreManager = new RestoreManager(
            $databaseManagerMock,
            $clientManagerMock,
            $processorManagerMock,
            $eventDispatcherMock,
            '',
            $filesystemMock,
            false
        );

        $restoreManager->execute();
    }

    /**
     * @test
     */
    public function shouldDispachRestoreFailedEventIfExceptionOccur()
    {
        $databaseManagerMock = $this->getMockBuilder(DatabaseManager::class)->disableOriginalConstructor()->getMock();
        $databaseManagerMock->expects($this->never())->method('restore');
        $clientManagerMock = $this->getMockBuilder(ClientManager::class)->disableOriginalConstructor()->getMock();
        $clientManagerMock->expects($this->never())->method('download');
        $processorManagerMock = $this->getMockBuilder(ProcessorManager::class)->disableOriginalConstructor()->getMock();
        $processorManagerMock->expects($this->never())->method('uncompress');
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects($this->any())->method('dispatch')->with(
            new \PHPUnit\Framework\Constraint\LogicalNot(RestoreEvent::RESTORE_COMPLETED)
        );
        $eventDispatcherMock->expects($this->once())->method('dispatch')->with(RestoreFailedEvent::RESTORE_FAILED);
        $filesystemMock = $this->createMock(Filesystem::class);
        $filesystemMock->expects($this->once())->method('mkdir')->will($this->throwException(new \Exception()));

        $restoreManager = new RestoreManager(
            $databaseManagerMock,
            $clientManagerMock,
            $processorManagerMock,
            $eventDispatcherMock,
            '',
            $filesystemMock,
            true
        );

        $restoreManager->execute();
    }
}
