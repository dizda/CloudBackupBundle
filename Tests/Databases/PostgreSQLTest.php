<?php

namespace Dizda\CloudBackupBundle\Tests\Databases;

use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class PostgreSQLTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Databases
 */
class PostgreSQLTest extends AbstractTesting
{
    /**
     * Test different commands
     */
    public function testGetCommand()
    {
        $postgresql = self::$kernel->getContainer()->get('dizda.cloudbackup.database.postgresql');

        // dump specified database
        $postgresql->__construct('localhost', 5678, 'dizbdd', 'admin', 'test', 'testcase1', array());
        $this->assertEquals($postgresql->getCommand(),
            'export PGPASSWORD="test" && pg_dump --username "admin" --host localhost --port 5678 --format plain --encoding UTF8 "dizbdd" > "dizbdd.sql"');

        // dump specified database
        $postgresql->__construct('somehost', 2222, 'somebdd', 'postgres', 'somepwd', 'testcase2', array());
        $this->assertEquals($postgresql->getCommand(),
            'export PGPASSWORD="somepwd" && pg_dump --username "postgres" --host somehost --port 2222 --format plain --encoding UTF8 "somebdd" > "somebdd.sql"');

        // dump specified database with no auth
        $postgresql->__construct('somehost', 2222, 'somebdd', null, null, 'testcase3', array());
        $this->assertEquals($postgresql->getCommand(),
            'pg_dump --host somehost --port 2222 --format plain --encoding UTF8 "somebdd" > "somebdd.sql"');

    }

}
