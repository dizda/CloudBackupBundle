<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Clients\ClientChain;
use Dizda\CloudBackupBundle\Clients\ClientInterface;
use Dizda\CloudBackupBundle\Databases\DatabaseChain;
use Dizda\CloudBackupBundle\Databases\DatabaseInterface;
use Dizda\CloudBackupBundle\Processors\ProcessorInterface;
use Monolog\Logger;

class BackupManager
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var \Dizda\CloudBackupBundle\Databases\DatabaseChain
     */
    private $database;

    /**
     * @var \Dizda\CloudBackupBundle\Clients\ClientChain
     */
    private $client;

    /**
     * @var \Dizda\CloudBackupBundle\Processors\ProcessorInterface
     */
    private $processor;

    /**
     * @param Logger $logger
     * @param DatabaseChain $database
     * @param ClientChain $client
     */
    public function __construct(Logger $logger, DatabaseInterface $database, ClientInterface $client)
    {
        $this->logger   = $logger;
        $this->database = $database;
        $this->client   = $client;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        try {
            // Dump all databases
            $this->database->dump();

            // Backup folders if specified
            $this->processor->copyFolders();

            // Compress everything
            $this->processor->compress();

            var_dump($this->processor->getArchivePath());

            // Transfer with all clients
            $this->client->upload($this->processor->getArchivePath());

            $this->processor->cleanUp();

        } catch (\Exception $e) {

            // write log
            $this->logger->critical($e);

            //Should we throw a general exception here? Maybe we should return a bool.
            throw $e;
        }
    }

    /**
     * Set the processor to compress files
     *
     * @param ProcessorInterface $processor
     */
    public function setProcessor(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }
}
