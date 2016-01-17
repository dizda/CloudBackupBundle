<?php

namespace Dizda\CloudBackupBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TaggedServicesPass.
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 * @author Tobias Nyholm
 */
class TaggedServicesPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->databaseCompilerPass($container);
        $this->clientCompilerPass($container);
        $this->processorCompilerPass($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function databaseCompilerPass(ContainerBuilder $container)
    {
        $databases = $container->findTaggedServiceIds('dizda.cloudbackup.database');
        $dbEnabled = $container->getParameter('dizda_cloud_backup.databases');

        $databaseManager = $container->getDefinition('dizda.cloudbackup.manager.database');

        foreach ($databases as $serviceId => $tags) {
            // Get the name of the database
            $name = explode('.', $serviceId);
            $name = end($name);

            if (!isset($dbEnabled[$name])) {
                continue;
            }

            // if the database is activated in the configuration file, we add it to the DatabaseChain
            $databaseManager->addMethodCall('add', array(new Reference($serviceId)));
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    public function clientCompilerPass(ContainerBuilder $container)
    {
        $clients        = $container->findTaggedServiceIds('dizda.cloudbackup.client');
        $clientsEnabled = $container->getParameter('dizda_cloud_backup.cloud_storages');

        $clientManager = $container->getDefinition('dizda.cloudbackup.manager.client');

        foreach ($clients as $serviceId => $tags) {
            // Get the name of the database
            $name = explode('.', $serviceId);
            $name = end($name);

            if (!isset($clientsEnabled[$name])) {
                continue;
            }

            // if the client is activated in the configuration file, we add it to the ClientChain
            $clientManager->addMethodCall('add', array(new Reference($serviceId)));
        }

        // If gaufrette is set, assign automatically the specified filesystem as the cloud client
        if (isset($container->getParameter('dizda_cloud_backup.cloud_storages')['gaufrette'])) {
            $filesystem = $container->getParameter('dizda_cloud_backup.cloud_storages')['gaufrette']['service_name'];
            foreach($filesystem as $filesystemName)
            {
                $gaufrette = $container->getDefinition('dizda.cloudbackup.client.gaufrette');
                $gaufrette->addMethodCall('addFilesystem', [
                    new Reference($filesystemName),
                ]);
            }
        }

        // If flysystem is set, assign automatically the specified filesystem adapters
        if (isset($container->getParameter('dizda_cloud_backup.cloud_storages')['flysystem'])) {
            $filesystem = $container->getParameter('dizda_cloud_backup.cloud_storages')['flysystem']['service_name'];

            foreach ($filesystem as $filesystemName) {
                $flysystem = $container->getDefinition('dizda.cloudbackup.client.flysystem');
                $flysystem->addMethodCall('addFilesystem', [
                    new Reference($filesystemName),
                ]);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    public function processorCompilerPass(ContainerBuilder $container)
    {
        $processors = $container->findTaggedServiceIds('dizda.cloudbackup.processor');
        $options = $container->getParameter('dizda_cloud_backup.processor');

        foreach ($processors as $serviceId => $tags) {
            $container->getDefinition($serviceId)->addMethodCall('addOptions', array($options['options']));
        }
    }
}
