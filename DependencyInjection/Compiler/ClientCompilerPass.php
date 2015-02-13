<?php

namespace Dizda\CloudBackupBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ClientCompilerPass
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class ClientCompilerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $clients        = $container->findTaggedServiceIds('dizda.cloudbackup.client');
        $clientsEnabled = $container->getParameter('dizda_cloud_backup.cloud_storages');

        $chainDefinition = $container->getDefinition('dizda.cloudbackup.chain.client');

        foreach ($clients as $serviceId => $tags) {
            // Get the name of the database
            $name = explode('.', $serviceId);
            $name = end($name);

            if (!isset($clientsEnabled[$name])) {
                continue;
            }

            // if the client is activated in the configuration file, we add it to the ClientChain
            $chainDefinition->addMethodCall('add', array(new Reference($serviceId)));
        }
//        if (isset($container->getParameter('dizda_cloud_backup.cloud_storages')['gaufrette'])) {
//            $filesystemName = $container->getParameter('dizda_cloud_backup.cloud_storages')['gaufrette']['service_name'];
//
//            $gaufrette = $container->get('dizda.cloudbackup.client.gaufrette');
//            $gaufrette->setFilesystem($container->get($filesystemName));
//        }
    }

}
