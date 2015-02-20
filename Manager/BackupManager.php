<?php

namespace Dizda\CloudBackupBundle\Manager;


use Dizda\CloudBackupBundle\Client\ClientInterface;
use Dizda\CloudBackupBundle\Database\DatabaseInterface;
use Monolog\Logger;

class BackupManager
{
    /**
     * @var \Monolog\Logger
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
     * @param Logger $logger
     * @param DatabaseManager $database
     * @param ClientManager $client
     * @param ProcessorManager $processor
     */
    public function __construct(Logger $logger, DatabaseManager $database, ClientManager $client, ProcessorManager $processor)
    {
        $this->logger    = $logger;
        $this->dbm       = $database;
        $this->cm        = $client;
        $this->processor = $processor;
    }

    /**
     * Start the backup
     *
     * @return bool
     */
    public function execute()
    {
        try {
            // Dump all databases
            $this->logger->info('[Dizda Backup] Starting to dump the database.', array('databases'=>$this->dbm->getName()));
            $this->dbm->dump();

            // Backup folders if specified
            $this->logger->info('[Dizda Backup] Copying folders.');
            $this->processor->copyFolders();

            // Compress everything
            $this->logger->info('[Dizda Backup] Compressing to archive.', array('processor'=>$this->processor->getName()));
            $this->processor->compress();

            var_dump($this->processor->getArchivePath());

            // Transfer with all clients
            $this->logger->info('[Dizda Backup] Uploading archive.', array('clients'=>$this->cm->getName()));
            $this->cm->upload($this->processor->getArchivePath());

            $this->logger->info('[Dizda Backup] Cleaning up after us.');
            $this->processor->cleanUp();

        } catch (\Exception $e) {
            // write log
            $this->logger->critical('[Dizda Backup] Unexpected exception.', array('exception'=>$e));

            return false;
        }

        return true;
    }
}
