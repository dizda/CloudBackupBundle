<?php

namespace Dizda\CloudBackupBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MongoDBTest
 *
 * @package Dizda\CloudBackupBundle\Tests\Databases
 */
class AbstractTesting extends WebTestCase
{

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
