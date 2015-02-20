<?php

namespace Dizda\CloudBackupBundle\Database;

use Dizda\CloudBackupBundle\Database\DatabaseInterface;

/**
 * Class DatabaseChain
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DatabaseChain implements DatabaseInterface
{
    /**
     * @var DatabaseInterface[] databases
     */
    protected $databases;

    /**
     * @param DatabaseInterface[] $databases
     */
    public function __construct(array $databases = array())
    {
        $this->databases = $databases;
    }

    /**
     * Add a database to the chain
     *
     * @param DatabaseInterface $database
     */
    public function add(DatabaseInterface $database)
    {
        $this->databases[] = $database;
    }

    /**
     * Dump all databases activated
     */
    public function dump()
    {
        foreach ($this->databases as $database) {
            $database->dump();
        }
    }

    public function getName()
    {
        $names = array();
        foreach ($this->databases as $database) {
            $names[] = $database->getName();
        }

        return sprintf('DatabaseChain of %d (%s)', count($names), implode(', ', $names));
    }
}