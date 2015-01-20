<?php

namespace Dizda\CloudBackupBundle\Tests\DependencyInjection;

use Dizda\CloudBackupBundle\DependencyInjection\DizdaCloudBackupExtension;
use Dizda\CloudBackupBundle\Tests\AbstractTesting;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DizdaCloudBackupExtensionTest extends AbstractTesting
{
    /**
     * Test google drive default configuration
     */
    public function testGoogleDriveDefaultConfiguration()
    {
        $container = self::$kernel->getContainerBuilder();
        $extension = new DizdaCloudBackupExtension();

        $extension->load(array(), $container);

//        $this->assertFalse($container->getParameter('dizda_cloud_backup.cloud_storages.google_drive.active'));
    }
}
