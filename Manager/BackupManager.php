<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Chain\ClientChain;
use Dizda\CloudBackupBundle\Chain\DatabaseChain;
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

        // Transfer with all clients
        $this->clientChain->upload();
    }
}
