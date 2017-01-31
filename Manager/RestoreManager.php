<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Event\RestoreEvent;
use Dizda\CloudBackupBundle\Event\RestoreFailedEvent;
use Dizda\CloudBackupBundle\Exception\RestoringNotAvailableException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

class RestoreManager
{
    /**
     * @var \Dizda\CloudBackupBundle\Manager\DatabaseManager
     */
    private $databaseManager;

    /**
     * @var \Dizda\CloudBackupBundle\Manager\ClientManager
     */
    private $clientManager;

    /**
     * @var \Dizda\CloudBackupBundle\Manager\ProcessorManager
     */
    private $processorManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $restoreFolder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var bool
     */
    private $doRestore;

    /**
     * @param DatabaseManager          $databaseManager
     * @param ClientManager            $clientManager
     * @param ProcessorManager         $processorManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string                   $restoreFolder
     * @param boolean                  $doRestore
     */
    public function __construct(
        DatabaseManager $databaseManager,
        ClientManager $clientManager,
        ProcessorManager $processorManager,
        EventDispatcherInterface $eventDispatcher,
        $restoreFolder,
        Filesystem $filesystem,
        $doRestore
    ) {
        $this->databaseManager = $databaseManager;
        $this->clientManager = $clientManager;
        $this->processorManager = $processorManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->restoreFolder = $restoreFolder;
        $this->filesystem = $filesystem;
        $this->doRestore = (boolean) $doRestore;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        if (!$this->doRestore) {
            throw RestoringNotAvailableException::create();
        }

        try {
            $this->filesystem->mkdir($this->restoreFolder);
            $file = $this->clientManager->download();
            $this->processorManager->uncompress($file);
            $this->databaseManager->restore();
            $this->eventDispatcher->dispatch(RestoreEvent::RESTORE_COMPLETED, new RestoreEvent($file));

            return true;
        } catch (\Exception $e) {
            $this->eventDispatcher->dispatch(RestoreFailedEvent::RESTORE_FAILED, new RestoreFailedEvent($e));
        }

        return false;
    }
}
