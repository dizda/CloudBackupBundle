<?php

namespace Dizda\CloudBackupBundle\Tests\Manager;

use Dizda\CloudBackupBundle\Event\BackupEvent;
use Dizda\CloudBackupBundle\Manager\BackupManager;

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class BackupManagerTest extends \PHPUnit\Framework\TestCase
{
	public function newGetMock($class){
		if(class_exists('\PHPUnit_Framework_TestCase')){
			$this->getMock($class);
		}else{
			$this->getMockBuilder($class);
		}
	}
    public function testBackupCompletedEventIsCalledOnSuccess()
    {
        $loggerMock = $this->newGetMock('Psr\Log\LoggerInterface');
        $databaseManagerMock = $this->newGetMock('Dizda\CloudBackupBundle\Manager\DatabaseManager')
            ->disableOriginalConstructor()->getMock();
        $clientManagerMock = $this->newGetMock('Dizda\CloudBackupBundle\Manager\ClientManager')
            ->disableOriginalConstructor()->getMock();
        $processorManagerMock = $this->newGetMock('Dizda\CloudBackupBundle\Manager\ProcessorManager')
            ->disableOriginalConstructor()->getMock();
        $eventDispatcherMock = $this->newGetMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcherMock->expects($this->once())->method('dispatch')->with(BackupEvent::BACKUP_COMPLETED);

        $backupManager = new BackupManager(
            $loggerMock, $databaseManagerMock, $clientManagerMock, $processorManagerMock, $eventDispatcherMock
        );
        $backupManager->execute();
    }

    public function testBackupCompletedEventIsNotCalledWhenFailed()
    {
        $loggerMock = $this->newGetMock('Psr\Log\LoggerInterface');
        $databaseManagerMock = $this->newGetMock('Dizda\CloudBackupBundle\Manager\DatabaseManager')
            ->disableOriginalConstructor()->getMock();
        $clientManagerMock = $this->newGetMock('Dizda\CloudBackupBundle\Manager\ClientManager')
            ->disableOriginalConstructor()->getMock();
        $processorManagerMock = $this->newGetMock('Dizda\CloudBackupBundle\Manager\ProcessorManager')
            ->disableOriginalConstructor()->getMock();
        $processorManagerMock->expects($this->once())->method('copyFolders')
            ->will($this->throwException(new \Exception()));
        $eventDispatcherMock = $this->newGetMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $eventDispatcherMock->expects($this->never())->method('dispatch')->with(BackupEvent::BACKUP_COMPLETED);

        $backupManager = new BackupManager(
            $loggerMock, $databaseManagerMock, $clientManagerMock, $processorManagerMock, $eventDispatcherMock
        );
        $backupManager->execute();
    }
}
