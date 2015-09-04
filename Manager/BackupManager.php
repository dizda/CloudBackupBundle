<?php

namespace Dizda\CloudBackupBundle\Manager;

use Psr\Log\LoggerInterface;
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
    private $dbm;

    /**
     * @var \Dizda\CloudBackupBundle\Manager\ClientManager
     */
    private $cm;

    /**
     * @var \Dizda\CloudBackupBundle\Manager\ProcessorManager
     */
    private $processor;

    /**
     * @param LoggerInterface  $logger
     * @param DatabaseManager  $database
     * @param ClientManager    $client
     * @param ProcessorManager $processor
     */
    public function __construct(
        LoggerInterface $logger,
        DatabaseManager $database,
        ClientManager $client,
        ProcessorManager $processor
    ) {
        $this->logger = $logger;
        $this->dbm = $database;
        $this->cm = $client;
        $this->processor = $processor;
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
            $this->dbm->dump();

            // Backup folders if specified
            $this->logger->info('[dizda-backup] Copying folders.');
            $this->processor->copyFolders();

            // Compress everything
            $this->logger->info(sprintf('[dizda-backup] Compressing to archive using %s', $this->processor->getName()));
            $this->processor->compress();

            // Transfer with all clients
            $this->cm->upload($this->processor->getArchivePath());
        } catch (\Exception $e) {
            // Write log
            $this->logger->critical('[dizda-backup] Unexpected exception.', array('exception' => $e));

            $successful = false;
        }

        try {
            // If we catch an exception or not, we would still like to try cleaning up after us
            $this->logger->info('[dizda-backup] Cleaning up after us.');
            $this->processor->cleanUp();
        } catch (IOException $e) {
            $this->logger->error('[dizda-backup] Cleaning up failed.');

            return false;
        }

        return $successful;
    }

    public function getClientManager()
    {
        return $this->cm;
    }

    public function getDatabaseManager()
    {
        return $this->dbm;
    }

    public function getProcessorManager()
    {
        return $this->processor;
    }
}
