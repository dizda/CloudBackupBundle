<?php

namespace Dizda\CloudBackupBundle\Tests\Database;

use Dizda\CloudBackupBundle\Database\MySQL;

/**
 * Class MySQLTest.
 */
class MySQLTest extends \PHPUnit_Framework_TestCase
{
    protected function checkConfigurationFileExistsAndValid($user, $password, $host, $port)
    {
        $filePath       = '/tmp/backup/mysql/mysql.cnf';
        $cnfFileContent = "[client]\nuser = \"$user\"\npassword = \"$password\"\nhost = \"$host\"\nport = \"$port\"\n";

        $this->assertFileExists($filePath);
        $this->assertContains(file_get_contents($filePath), $cnfFileContent);
    }

    /**
     * @test
     */
    public function shouldDumpAllDatabases()
    {
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => true,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'test',
            ),
        ), '/tmp/backup/');

        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');
        $this->assertFileExists('/tmp/backup/mysql/mysql.cnf');
        $this->assertEquals($mysql->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" --all-databases  > '/tmp/backup/mysql/all-databases.sql'");
    }

    /**
     * @test
     */
    public function shouldDumpSpecifiedDatabase()
    {
        $mysql1 = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'test',
            ),
        ), '/tmp/backup/');

        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');
        $this->assertEquals($mysql1->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" dizbdd  > '/tmp/backup/mysql/dizbdd.sql'");
        
        $mysql2 = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'db_host'       => 'somehost',
                'db_port'       => 2222,
                'database'      => 'somebdd',
                'db_user'       => 'mysql',
                'db_password'   => 'somepwd',
            ),
        ), '/tmp/backup/');

        $this->checkConfigurationFileExistsAndValid('mysql', 'somepwd', 'somehost', '2222');
        $this->assertEquals($mysql2->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" somebdd  > '/tmp/backup/mysql/somebdd.sql'");

        // dump specified database with no auth
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'db_host'       => 'somehost',
                'db_port'       => 2222,
                'database'      => 'somebdd',
                'db_user'       => null,
                'db_password'   => null,
            ),
        ), '/tmp/backup/');

        $this->assertEquals($mysql->getCommand(), 'mysqldump  somebdd  > \'/tmp/backup/mysql/somebdd.sql\'');
    }

    /**
     * @test
     */
    public function shouldDumpAllDatabasesWithNoAuth()
    {
        // dump all databases with no auth
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => true,
                'db_host'       => 'somehost',
                'db_port'       => 2222,
                'database'      => 'somebdd',
                'db_user'       => null,
                'db_password'   => null,
            ),
        ), '/tmp/backup/');

        $this->assertEquals($mysql->getCommand(), 'mysqldump  --all-databases  > \'/tmp/backup/mysql/all-databases.sql\'');
    }

    /**
     * @test
     */
    public function shouldIgnoreSpecifiedTablesForSpecifiedDatabase()
    {
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'test',
                'ignore_tables' => array('table1', 'table2'),
            ),
        ), '/tmp/backup/');

        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');
        $this->assertEquals($mysql->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" dizbdd --ignore-table=dizbdd.table1 --ignore-table=dizbdd.table2  > '/tmp/backup/mysql/dizbdd.sql'");
    }

    /**
     * @test
     */
    public function shouldIgnoreSpecifiedTablesForAllDatabase()
    {
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => true,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => null,
                'db_user'       => 'root',
                'db_password'   => 'test',
                'ignore_tables' => array('db1.table1', 'db2.table2'),
            ),
        ), '/tmp/backup/');

        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');
        $this->assertEquals($mysql->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" --all-databases --ignore-table=db1.table1 --ignore-table=db2.table2  > '/tmp/backup/mysql/all-databases.sql'");
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function shouldThrowExceptionIfDatabaseIsNotSpecifiedForIgnoredTableDumpingAllDatabases()
    {
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => true,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => null,
                'db_user'       => 'root',
                'db_password'   => 'test',
                'ignore_tables' => array('table1'),
            ),
        ), '/tmp/backup/');

        $mysql->getCommand();
    }
}

class MySQLDummy extends MySQL
{
    public function getCommand()
    {
        return parent::getCommand();
    }
}
