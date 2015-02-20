<?php

namespace Dizda\CloudBackupBundle\Database;

/**
 * Interface DatabaseInterface
 *
 * @package Dizda\CloudBackupBundle\Database
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */

interface DatabaseInterface
{
    /**
     * Migration procedure for each databases type
     *
     * @return void
     */
    public function dump();

    /**
     * The name of the database
     *
     * @return string
     */
    public function getName();
}
