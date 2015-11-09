<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Event\BackupEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOException;

class BackupManager
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

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
     * @param LoggerInterface          $logger
     * @param DatabaseManager          $databaseManager
     * @param ClientManager            $clientManager
     * @param ProcessorManager         $processorManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        LoggerInterface $logger,
        DatabaseManager $databaseManager,
        ClientManager $clientManager,
        ProcessorManager $processorManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->logger = $logger;
        $this->databaseManager = $databaseManager;
        $this->clientManager = $clientManager;
        $this->processorManager = $processorManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Start the backup.
     *
     * @return bool
     */
    public function execute()
    {
        $successful = true;
        try {
            // Dump all databases
            $this->databaseManager->dump();

            // Backup folders if specified
            $this->logger->info('[dizda-backup] Copying folders.');
            $this->processorManager->copyFolders();

            // Compress everything
            $this->logger->info(sprintf('[dizda-backup] Compressing to archive using %s', $this->processorManager->getName()));
            $this->processorManager->compress();

            // Transfer with all clients
            $this->clientManager->upload($this->processorManager->getArchivePath());
        } catch (\Exception $e) {
            // Write log
            $this->logger->critical('[dizda-backup] Unexpected exception.', array('exception' => $e));

            $successful = false;
        }

        try {
            // If we catch an exception or not, we would still like to try cleaning up after us
            $this->logger->info('[dizda-backup] Cleaning up after us.');
            $this->processorManager->cleanUp();
        } catch (IOException $e) {
            $this->logger->error('[dizda-backup] Cleaning up failed.', array('exception' => $e));

            return false;
        }

        if ($successful) {
            $this->eventDispatcher->dispatch(BackupEvent::BACKUP_COMPLETED, new BackupEvent());
        }

        return $successful;
    }

    /**
     * @deprecated
     *
     * @return ClientManager
     */
    public function getClientManager()
    {
        return $this->clientManager;
    }

    /**
     * @deprecated
     *
     * @return DatabaseManager
     */
    public function getDatabaseManager()
    {
        return $this->databaseManager;
    }

    /**
     * @deprecated
     *
     * @return ProcessorManager
     */
    public function getProcessorManager()
    {
        return $this->processorManager;
    }
}
