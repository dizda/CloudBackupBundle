<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Chain\DatabaseChain;
use Monolog\Logger;

class BackupManager
{
    private $logger;

    /**
     * @var \Dizda\CloudBackupBundle\Chain\DatabaseChain
     */
    private $databaseChain;

    public function __construct(Logger $logger, DatabaseChain $databaseChain)
    {
        $this->databaseChain = $databaseChain;
    }

    public function execute()
    {
        // Dump all databases
        $this->databaseChain->dump();


    }
}
