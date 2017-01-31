<?php

namespace Dizda\CloudBackupBundle\Database;

interface RestorableDatabaseInterface
{
    /**
     * Restore the database with a previous dump
     */
    public function restore();
}
