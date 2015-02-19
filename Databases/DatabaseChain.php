<?php

namespace Dizda\CloudBackupBundle\Databases;

use Dizda\CloudBackupBundle\Databases\DatabaseInterface;

/**
 * Class DatabaseChain
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DatabaseChain implements DatabaseInterface
{
    /**
     * @var array
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
}