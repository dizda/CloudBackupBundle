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
//        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
//
//        $containerMock->expects($this->any())->method('setParameter');
//
//        $containerMock->expects($this->once())
//            ->method('setParameter')
//            ->with($this->equalTo('dizda_cloud_backup.cloud_storages.google_drive.active'), $this->equalTo(false));

        $container = self::$kernel->getContainerBuilder();
        $extension = new DizdaCloudBackupExtension();

        $extension->load([], $container);

        $this->assertFalse($container->getParameter('dizda_cloud_backup.cloud_storages.google_drive.active'));
    }
} 