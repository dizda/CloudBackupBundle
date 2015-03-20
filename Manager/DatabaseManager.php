<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Database\DatabaseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DatabaseChain.
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DatabaseManager
{
    /**
     * @var DatabaseInterface[] links
     */
    protected $children;

    /**
     * @var \Psr\Log\LoggerInterface logger
     */
    protected $logger;

    /**
     * @param LoggerInterface     $logger
     * @param DatabaseInterface[] $databases
     */
    public function __construct(LoggerInterface $logger, array $databases = array())
    {
        $this->logger = $logger;
        $this->children = $databases;
    }

    /**
     * Add a database to the chain.
     *
     * @param DatabaseInterface $database
     */
    public function add(DatabaseInterface $database)
    {
        $this->children[] = $database;
    }

    /**
     * Dump all databases activated.
     */
    public function dump()
    {
        foreach ($this->children as $child) {
            $this->logger->info(sprintf('[dizda-backup] Dumping %s database', $child->getName()));
            $child->dump();
        }
    }
}
