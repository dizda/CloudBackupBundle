<?php

namespace Dizda\CloudBackupBundle\Tests\Databases;

use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class MySQLTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Databases
 */
class MySQLTest extends AbstractTesting
{
    /**
     * Test different commands
     */
    public function testGetCommand()
    {
        $mysql = self::$kernel->getContainer()->get('dizda.cloudbackup.database.mysql');

        // dump all databases
        $mysql->__construct(true, 'localhost', 3306, 'dizbdd', 'root', 'test', '/var/backup/');
        $this->assertEquals($mysql->getCommand(), "mysqldump --host='localhost' --port='3306' --user='root' --password='test' --all-databases > /var/backup/mysql/all-databases.sql");

        // dump specified database
        $mysql->__construct(false, 'localhost', 3306, 'dizbdd', 'root', 'test', '/var/backup/');
        $this->assertEquals($mysql->getCommand(), "mysqldump --host='localhost' --port='3306' --user='root' --password='test' dizbdd > /var/backup/mysql/dizbdd.sql");

        // dump specified database
        $mysql->__construct(false, 'somehost', 2222, 'somebdd', 'mysql', 'somepwd', '/var/backup/');
        $this->assertEquals($mysql->getCommand(), "mysqldump --host='somehost' --port='2222' --user='mysql' --password='somepwd' somebdd > /var/backup/mysql/somebdd.sql");

        // dump specified database with no auth
        $mysql->__construct(false, 'somehost', 2222, 'somebdd', null, null, '/var/backup/');
        $this->assertEquals($mysql->getCommand(), 'mysqldump  somebdd > /var/backup/mysql/somebdd.sql');

        // dump all databases with no auth
        $mysql->__construct(true, 'somehost', 2222, 'somebdd', null, null, '/var/backup/');
        $this->assertEquals($mysql->getCommand(), 'mysqldump  --all-databases > /var/backup/mysql/all-databases.sql');
    }

}
