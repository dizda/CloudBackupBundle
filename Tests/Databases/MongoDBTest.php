<?php

namespace Dizda\CloudBackupBundle\Tests\Databases;

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
        $mongodb = self::$kernel->getContainer()->get('dizda.cloudbackup.database.mongodb');

        // dump all dbs
        $mongodb->__construct(true, 'localhost', 27017, 'dizbdd', null, null);
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017  --out ');

        // dump one db with not auth
        $mongodb->__construct(false, 'localhost', 27017, 'dizbdd', null, null);
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017 --db dizbdd --out ');

        // dump one db with auth
        $mongodb->__construct(false, 'localhost', 27017, 'dizbdd', 'dizda', 'imRootBro');
        $this->assertEquals($mongodb->getCommand(), 'mongodump -h localhost --port 27017 -u dizda -p imRootBro --db dizbdd --out ');
    }

}
