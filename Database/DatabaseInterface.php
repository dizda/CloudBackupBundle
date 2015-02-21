<?php

namespace Dizda\CloudBackupBundle\Database;

/**
 * Interface DatabaseInterface.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
interface DatabaseInterface
{
    /**
     * Migration procedure for each databases type.
     */
    public function dump();

    /**
     * The name of the database.
     *
     * @return string
     */
    public function getName();
}
