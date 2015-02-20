<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Database\DatabaseInterface;

/**
 * Class DatabaseChain
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DatabaseManager
{
    /**
     * @var DatabaseInterface[] links
     *
     */
    protected $children;

    /**
     * @param DatabaseInterface[] $databases
     */
    public function __construct(array $databases = array())
    {
        $this->children = $databases;
    }

    /**
     * Add a database to the chain
     *
     * @param DatabaseInterface $database
     */
    public function add(DatabaseInterface $database)
    {
        $this->children[] = $database;
    }

    /**
     * Dump all databases activated
     */
    public function dump()
    {
        foreach ($this->children as $child) {
            $child->dump();
        }
    }

    public function getName()
    {
        $names = array();
        foreach ($this->children as $child) {
            $names[] = $child->getName();
        }

        return sprintf('DatabaseChain of %d (%s)', count($names), implode(', ', $names));
    }
}