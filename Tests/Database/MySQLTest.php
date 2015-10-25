<?php

namespace Dizda\CloudBackupBundle\Tests\Database;

use Dizda\CloudBackupBundle\Database\MySQL;

/**
 * Class MySQLTest.
 */
class MySQLTest extends \PHPUnit_Framework_TestCase
{
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
        ), '/var/backup/');
        $this->assertEquals($mysql->getCommand(), "mysqldump --host=\"localhost\" --port=\"3306\" --user=\"root\" --password=\"test\" --all-databases > /var/backup/mysql/all-databases.sql");
        

        $this->assertEquals($mysql->getCommand(), "mysqldump --host='localhost' --port='3306' --user='root' --password='test' --all-databases  > /var/backup/mysql/all-databases.sql");
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
        ), '/var/backup/');
        $this->assertEquals($mysql->getCommand(), "mysqldump --host=\"localhost\" --port=\"3306\" --user=\"root\" --password=\"test\" dizbdd > /var/backup/mysql/dizbdd.sql");
        

        $mysql2 = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'db_host'       => 'somehost',
                'db_port'       => 2222,
                'database'      => 'somebdd',
                'db_user'       => 'mysql',
                'db_password'   => 'somepwd',
            ),
        ), '/var/backup/');
        
        $this->assertEquals($mysql1->getCommand(), "mysqldump --host='localhost' --port='3306' --user='root' --password='test' dizbdd  > /var/backup/mysql/dizbdd.sql");
        $this->assertEquals($mysql2->getCommand(), "mysqldump --host=\"somehost\" --port=\"2222\" --user=\"mysql\" --password='\"somepwd\" somebdd > /var/backup/mysql/somebdd.sql");
    /**
     * @test
     */
    public function shouldDumpSpecifiedDatabaseWithNoAuth()
    {
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
        ), '/var/backup/');

        $this->assertEquals($mysql->getCommand(), 'mysqldump  somebdd  > /var/backup/mysql/somebdd.sql');
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
        ), '/var/backup/');

        $this->assertEquals($mysql->getCommand(), 'mysqldump  --all-databases  > /var/backup/mysql/all-databases.sql');
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
        ), '/var/backup/');

        $this->assertEquals($mysql->getCommand(), "mysqldump --host='localhost' --port='3306' --user='root' --password='test' dizbdd --ignore-table=dizbdd.table1 --ignore-table=dizbdd.table2  > /var/backup/mysql/dizbdd.sql");
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
        ), '/var/backup/');

        $this->assertEquals($mysql->getCommand(), "mysqldump --host='localhost' --port='3306' --user='root' --password='test' --all-databases --ignore-table=db1.table1 --ignore-table=db2.table2  > /var/backup/mysql/all-databases.sql");
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
        ), '/var/backup/');

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
