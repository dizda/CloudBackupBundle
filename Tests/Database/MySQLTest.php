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
        $cnfFileContent = "[client]\n";
        $cnfFileContent .= $user ? "user = \"$user\"\n" : "";
        $cnfFileContent .= $password ? "password = \"$password\"\n" : "";
        $cnfFileContent .= $host ? "host = \"$host\"\n" : "";
        $cnfFileContent .= $port ? "port = \"$port\"\n" : "";

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
                'single_transaction' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'test',
            ),
        ));

        $this->assertEquals($mysql->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" --all-databases  > '/tmp/backup/mysql/all-databases.sql'");
        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');
    }

    /**
     * @test
     */
    public function shouldDumpSpecifiedDatabase()
    {
        $mysql1 = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'single_transaction' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'test',
            ),
        ));

        $this->assertEquals($mysql1->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" dizbdd  > '/tmp/backup/mysql/dizbdd.sql'");
        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');

        $mysql2 = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'single_transaction' => false,
                'db_host'       => 'somehost',
                'db_port'       => 2222,
                'database'      => 'somebdd',
                'db_user'       => 'mysql',
                'db_password'   => 'somepwd',
            ),
        ));

        $this->assertEquals($mysql2->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" somebdd  > '/tmp/backup/mysql/somebdd.sql'");
        $this->checkConfigurationFileExistsAndValid('mysql', 'somepwd', 'somehost', '2222');

        // dump specified database with no auth
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'single_transaction' => false,
                'db_host'       => 'somehost',
                'db_port'       => 2222,
                'database'      => 'somebdd',
                'db_user'       => null,
                'db_password'   => null,
            ),
        ));

        $this->assertEquals($mysql->getCommand(), 'mysqldump --defaults-extra-file="/tmp/backup/mysql/mysql.cnf" somebdd  > \'/tmp/backup/mysql/somebdd.sql\'');
        $this->checkConfigurationFileExistsAndValid(null, null, 'somehost', '2222');
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
                'single_transaction' => false,
                'db_host'       => 'somehost',
                'db_port'       => 2222,
                'database'      => 'somebdd',
                'db_user'       => null,
                'db_password'   => null,
            ),
        ));

        $this->assertEquals($mysql->getCommand(), 'mysqldump --defaults-extra-file="/tmp/backup/mysql/mysql.cnf" --all-databases  > \'/tmp/backup/mysql/all-databases.sql\'');
        $this->checkConfigurationFileExistsAndValid(null, null, 'somehost', '2222');
    }

    /**
     * @test
     */
    public function shouldIgnoreSpecifiedTablesForSpecifiedDatabase()
    {
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => false,
                'single_transaction' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'test',
                'ignore_tables' => array('table1', 'table2'),
            ),
        ));

        $this->assertEquals($mysql->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" dizbdd --ignore-table=dizbdd.table1 --ignore-table=dizbdd.table2  > '/tmp/backup/mysql/dizbdd.sql'");
        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');
    }

    /**
     * @test
     */
    public function shouldIgnoreSpecifiedTablesForAllDatabase()
    {
        $mysql = new MySQLDummy(array(
            'mysql' => array(
                'all_databases' => true,
                'single_transaction' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => null,
                'db_user'       => 'root',
                'db_password'   => 'test',
                'ignore_tables' => array('db1.table1', 'db2.table2'),
            ),
        ));

        $this->assertEquals($mysql->getCommand(), "mysqldump --defaults-extra-file=\"/tmp/backup/mysql/mysql.cnf\" --all-databases --ignore-table=db1.table1 --ignore-table=db2.table2  > '/tmp/backup/mysql/all-databases.sql'");
        $this->checkConfigurationFileExistsAndValid('root', 'test', 'localhost', '3306');
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
                'single_transaction' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => null,
                'db_user'       => 'root',
                'db_password'   => 'test',
                'ignore_tables' => array('table1'),
            ),
        ));

        $mysql->getCommand();
    }

    /**
     * @test
     */
    public function shouldReturnRestoreCommand()
    {
        $mysql = new MySQLDummy([
            'mysql' => [
                'all_databases' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => null,
            ],
        ]);

        $this->assertEquals('mysql -uroot -hlocalhost -P3306 dizbdd < \'/tmp/restore/mysql/dizbdd.sql\'', $mysql->getRestoreCommand());
    }

    /**
     * @test
     */
    public function shouldReturnRestoreCommandWithPassword()
    {
        $mysql = new MySQLDummy([
            'mysql' => [
                'all_databases' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'foobar',
            ],
        ]);

        $this->assertEquals('mysql -uroot --password="foobar" -hlocalhost -P3306 dizbdd < \'/tmp/restore/mysql/dizbdd.sql\'', $mysql->getRestoreCommand());
    }

    /**
     * @test
     * @expectedException \Dizda\CloudBackupBundle\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Parameter "$restoreFolder" is not set.
     */
    public function throwExceptionIfRestoreFolderIsNotConfigured()
    {
        $mysql = new MySQLDummy([
            'mysql' => [
                'all_databases' => false,
                'db_host'       => 'localhost',
                'db_port'       => 3306,
                'database'      => 'dizbdd',
                'db_user'       => 'root',
                'db_password'   => 'foobar',
            ],
        ], '/tmp/backup/', null);

        $mysql->getRestoreCommand();
    }
}

class MySQLDummy extends MySQL
{
    public function __construct(array $params, $basePath = '/tmp/backup/', $restoreFolder = '/tmp/restore/')
    {
        parent::__construct($params, $basePath, $restoreFolder);
    }

    public function getCommand()
    {
        $this->prepareEnvironment();
        return parent::getCommand();
    }

    public function getRestoreCommand()
    {
        return parent::getRestoreCommand();
    }
}
