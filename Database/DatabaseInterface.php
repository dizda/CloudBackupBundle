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
     * @return mixed
     */
    public function dump();

}
