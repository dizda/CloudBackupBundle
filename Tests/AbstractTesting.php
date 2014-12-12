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
    protected static $kernel;

    /**
     * Setup the kernel.
     *
     * @return null
     */
    public function setUp()
    {
        $kernel = '\\Dizda\\CloudBackupBundle\\Tests\\Sandbox\\app\\AppKernel';

        self::$kernel = new $kernel('test', true);
        self::$kernel->boot();
    }

}