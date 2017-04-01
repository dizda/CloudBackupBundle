<?php

namespace Dizda\CloudBackupBundle\Tests\Database;

use Dizda\CloudBackupBundle\Database\PostgreSQL;

/**
 * Class PostgreSQLTest.
 */
// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}
class PostgreSQLTest extends \PHPUnit\Framework\TestCase
{
	public function newGetMock($class){
		if(!class_exists('\PHPUnit\Framework\TestCase')){
			$this->getMock($class);
		}else{
			$this->getMockBuilder($class);
		}
	}
    /**
     * Test different commands.
     */
    public function testGetCommand()
    {
        // dump specified database
        $postgresql = new PostgreSQLDummy(array(
            'postgresql' => array(
                'all_databases' => true,
                'db_host'     => 'localhost',
                'db_port'     => 5678,
                'database'    => 'dizbdd',
                'db_user'     => 'admin',
                'db_password' => 'test',
            ),
        ), '/var/backup/');
        $this->assertEquals($postgresql->getCommand(),
            'export PGPASSWORD="test" && pg_dump --username "admin" --host localhost --port 5678 --format plain --encoding UTF8 "dizbdd" > "/var/backup/postgresql/dizbdd.sql"');

        // dump specified database
        $postgresql = new PostgreSQLDummy(array(
            'postgresql' => array(
                'all_databases' => true,
                'db_host'     => 'somehost',
                'db_port'     => 2222,
                'database'    => 'somebdd',
                'db_user'     => 'postgres',
                'db_password' => 'somepwd',
            ),
        ), '/var/backup/');
        $this->assertEquals($postgresql->getCommand(),
            'export PGPASSWORD="somepwd" && pg_dump --username "postgres" --host somehost --port 2222 --format plain --encoding UTF8 "somebdd" > "/var/backup/postgresql/somebdd.sql"');

        // dump specified database with no auth
        $postgresql = new PostgreSQLDummy(array(
            'postgresql' => array(
                'all_databases' => false,
                'db_host'     => 'somehost',
                'db_port'     => 2222,
                'database'    => 'somebdd',
                'db_user'     => null,
                'db_password' => null,
            ),
        ), '/var/backup/');
        $this->assertEquals($postgresql->getCommand(),
            'pg_dump --host somehost --port 2222 --format plain --encoding UTF8 "somebdd" > "/var/backup/postgresql/somebdd.sql"');
    }
}

class PostgreSQLDummy extends PostgreSQL
{
    public function getCommand()
    {
        return parent::getCommand();
    }
}
