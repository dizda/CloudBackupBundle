<?php

namespace Dizda\CloudBackupBundle\Tests\Database;

use Dizda\CloudBackupBundle\Database\MongoDB;

/**
 * Class MongoDBTest.
 */
class MongoDBTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test different commands.
     */
    public function testGetCommand()
    {
        // dump all dbs
        $mongodb = new MongoDBDummy(array(
            'mongodb' => array(
                'all_databases' => true,
                'db_host'     => 'localhost',
                'db_port'     => 27017,
                'database'    => 'dizbdd',
                'db_user'     => null,
                'db_password' => null,
            ),
        ), '/var/backup/');
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017  --out /var/backup/mongo/');

        // dump one db with not auth
        $mongodb = new MongoDBDummy(array(
            'mongodb' => array(
                'all_databases' => false,
                'db_host'     => 'localhost',
                'db_port'     => 27017,
                'database'    => 'dizbdd',
                'db_user'     => null,
                'db_password' => null,
            ),
        ), '/var/backup/');
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017 --db dizbdd --out /var/backup/mongo/');

        // dump one db with auth
        $mongodb = new MongoDBDummy(array(
            'mongodb' => array(
                'all_databases' => false,
                'db_host'     => 'localhost',
                'db_port'     => 27017,
                'database'    => 'dizbdd',
                'db_user'     => 'dizda',
                'db_password' => 'imRootBro',
            ),
        ), '/var/backup/');
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017 -u dizda -p imRootBro --db dizbdd --out /var/backup/mongo/');
    }
}

class MongoDBDummy extends MongoDB
{
    public function getCommand()
    {
        return parent::getCommand();
    }
}
