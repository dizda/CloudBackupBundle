<?php

namespace Dizda\CloudBackupBundle\Databases;

/**
 * Interface DatabaseInterface
 *
 * @package Dizda\CloudBackupBundle\Databases
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
