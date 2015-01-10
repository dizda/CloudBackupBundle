<?php

namespace Dizda\CloudBackupBundle\Tests\Databases;

use Dizda\CloudBackupBundle\Databases\MongoDB;
use Dizda\CloudBackupBundle\Tests\AbstractTesting;

/**
 * Class MongoDBTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Databases
 */
class MongoDBTest extends AbstractTesting
{
    /**
     * Test different commands
     */
    public function testGetCommand()
    {
        // dump all dbs
        $mongodb = new MongoDB([
            'mongodb' => [
                'all_databases' => true,
                'db_host'     => 'localhost',
                'db_port'     => 27017,
                'database'    => 'dizbdd',
                'db_user'     => null,
                'db_password' => null
            ]
        ], '/var/backup/');
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017  --out /var/backup/mongo/');

        // dump one db with not auth
        $mongodb = new MongoDB([
            'mongodb' => [
                'all_databases' => false,
                'db_host'     => 'localhost',
                'db_port'     => 27017,
                'database'    => 'dizbdd',
                'db_user'     => null,
                'db_password' => null
            ]
        ], '/var/backup/');
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017 --db dizbdd --out /var/backup/mongo/');

        // dump one db with auth
        $mongodb = new MongoDB([
            'mongodb' => [
                'all_databases' => false,
                'db_host'     => 'localhost',
                'db_port'     => 27017,
                'database'    => 'dizbdd',
                'db_user'     => 'dizda',
                'db_password' => 'imRootBro'
            ]
        ], '/var/backup/');
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017 -u dizda -p imRootBro --db dizbdd --out /var/backup/mongo/');
    }

}
