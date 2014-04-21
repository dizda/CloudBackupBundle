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
        $mysql->__construct(true, 'localhost', 3306, 'dizbdd', 'root', 'test', 'localhost', array());

        // dump all databases
        $this->assertEquals($mysql->getCommand(), 'mysqldump --host=localhost --port=3306 --user=root --password=test --all-databases > all-databases.sql');

        // dump specified database
        $mysql->__construct(false, 'localhost', 3306, 'dizbdd', 'root', 'test', 'localhost', array());
        $this->assertEquals($mysql->getCommand(), 'mysqldump --host=localhost --port=3306 --user=root --password=test dizbdd > dizbdd.sql');

        // dump specified database
        $mysql->__construct(false, 'somehost', 2222, 'somebdd', 'mysql', 'somepwd', 'localhost', array());
        $this->assertEquals($mysql->getCommand(), 'mysqldump --host=somehost --port=2222 --user=mysql --password=somepwd somebdd > somebdd.sql');

        // dump specified database with no auth
        $mysql->__construct(false, 'somehost', 2222, 'somebdd', null, null, 'localhost', array());
        $this->assertEquals($mysql->getCommand(), 'mysqldump  somebdd > somebdd.sql');

        // dump all databases with no auth
        $mysql->__construct(true, 'somehost', 2222, 'somebdd', null, null, 'localhost', array());
        $this->assertEquals($mysql->getCommand(), 'mysqldump  --all-databases > all-databases.sql');
    }

}
