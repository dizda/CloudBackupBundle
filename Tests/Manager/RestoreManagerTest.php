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

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class RestoreManagerTest extends \PHPUnit\Framework\TestCase
{
	public function newGetMock($class){
		if(!class_exists('\PHPUnit\Framework\TestCase')){
			$this->getMock($class);
		}else{
			$this-getMockBuilder($class);
		}
	}
    /**
     * @test
     */
    public function shouldRestoreDatabase()
    {
        $fileMock = $this
            ->getMockBuilder(\SplFileInfo::class)
            ->setConstructorArgs([tempnam(sys_get_temp_dir(), '')])
            ->getMock();

        $databaseManagerMock = $this->newGetMock(DatabaseManager::class)->disableOriginalConstructor()->getMock();
        $databaseManagerMock->expects($this->once())->method('restore');
        $clientManagerMock = $this->newGetMock(ClientManager::class)->disableOriginalConstructor()->getMock();
        $clientManagerMock->expects($this->once())->method('download')->willReturn($fileMock);
        $processorManagerMock = $this->newGetMock(ProcessorManager::class)->disableOriginalConstructor()->getMock();
        $processorManagerMock->expects($this->once())->method('uncompress');
        $eventDispatcherMock = $this->newGetMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects($this->once())->method('dispatch')->with(RestoreEvent::RESTORE_COMPLETED);
        $filesystemMock = $this->newGetMock(Filesystem::class);

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
        $databaseManagerMock = $this->newGetMock(DatabaseManager::class)->disableOriginalConstructor()->getMock();
        $databaseManagerMock->expects($this->never())->method('restore');
        $clientManagerMock = $this->newGetMock(ClientManager::class)->disableOriginalConstructor()->getMock();
        $clientManagerMock->expects($this->never())->method('download');
        $processorManagerMock = $this->newGetMock(ProcessorManager::class)->disableOriginalConstructor()->getMock();
        $processorManagerMock->expects($this->never())->method('uncompress');
        $eventDispatcherMock = $this->newGetMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects($this->never())->method('dispatch');
        $filesystemMock = $this->newGetMock(Filesystem::class);

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
        $databaseManagerMock = $this->newGetMock(DatabaseManager::class)->disableOriginalConstructor()->getMock();
        $databaseManagerMock->expects($this->never())->method('restore');
        $clientManagerMock = $this->newGetMock(ClientManager::class)->disableOriginalConstructor()->getMock();
        $clientManagerMock->expects($this->never())->method('download');
        $processorManagerMock = $this->newGetMock(ProcessorManager::class)->disableOriginalConstructor()->getMock();
        $processorManagerMock->expects($this->never())->method('uncompress');
        $eventDispatcherMock = $this->newGetMock(EventDispatcherInterface::class);
        $eventDispatcherMock->expects($this->any())->method('dispatch')->with(
            new \PHPUnit_Framework_Constraint_Not(RestoreEvent::RESTORE_COMPLETED)
        );
        $eventDispatcherMock->expects($this->once())->method('dispatch')->with(RestoreFailedEvent::RESTORE_FAILED);
        $filesystemMock = $this->newGetMock(Filesystem::class);
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
