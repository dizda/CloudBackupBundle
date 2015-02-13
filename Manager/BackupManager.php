<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Chain\ClientChain;
use Dizda\CloudBackupBundle\Chain\DatabaseChain;
use Dizda\CloudBackupBundle\Processors\ProcessorInterface;
use Monolog\Logger;

class BackupManager
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var \Dizda\CloudBackupBundle\Chain\DatabaseChain
     */
    private $databaseChain;

    /**
     * @var \Dizda\CloudBackupBundle\Chain\ClientChain
     */
    private $clientChain;

    private $processor;

    public function __construct(Logger $logger, DatabaseChain $databaseChain, ClientChain $clientChain)
    {
        $this->logger        = $logger;
        $this->databaseChain = $databaseChain;
        $this->clientChain   = $clientChain;
    }

    public function execute()
    {
        // Dump all databases
        $this->databaseChain->dump();

        $this->processor->compress();

        $wholeFile = $this->processor->getArchivePath();

        // Transfer with all clients
        $this->clientChain->upload($this->processor->getArchivePath());
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
