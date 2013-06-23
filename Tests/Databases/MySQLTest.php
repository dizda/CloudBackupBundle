<?php

namespace Dizda\CloudBackupBundle\Tests\Databases;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MySQLTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Databases
 */
class MySQLTest extends WebTestCase
{
    /**
     * Test different commands
     */
    public function testGetCommand()
    {
        $mysql = self::$kernel->getContainer()->get('dizda.cloudbackup.database.mysql');
        $mysql->__construct(true, 'localhost', 3306, 'dizbdd', 'root', 'test');

        // dump all databases
        $this->assertEquals($mysql->getCommand(), 'mysqldump --host=localhost --port=3306 --user=root --password=test --all-databases > all-databases.sql');

        // dump specified database
        $mysql->__construct(false, 'localhost', 3306, 'dizbdd', 'root', 'test');
        $this->assertEquals($mysql->getCommand(), 'mysqldump --host=localhost --port=3306 --user=root --password=test dizbdd > dizbdd.sql');

        // dump specified database
        $mysql->__construct(false, 'somehost', 2222, 'somebdd', 'mysql', 'somepwd');
        $this->assertEquals($mysql->getCommand(), 'mysqldump --host=somehost --port=2222 --user=mysql --password=somepwd somebdd > somebdd.sql');

        // dump specified database with no auth
        $mysql->__construct(false, 'somehost', 2222, 'somebdd', null, null);
        $this->assertEquals($mysql->getCommand(), 'mysqldump  somebdd > somebdd.sql');

        // dump all databases with no auth
        $mysql->__construct(true, 'somehost', 2222, 'somebdd', null, null);
        $this->assertEquals($mysql->getCommand(), 'mysqldump  --all-databases > all-databases.sql');
    }

    /**
     * Setup the kernel.
     *
     * @return null
     */
    public function setUp()
    {
        $kernel = self::getKernelClass();

        self::$kernel = new $kernel('test', true);
        self::$kernel->boot();
    }
}
