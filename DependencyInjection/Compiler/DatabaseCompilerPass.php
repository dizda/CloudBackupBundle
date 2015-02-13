<?php

namespace Dizda\CloudBackupBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DatabaseCompilerPass
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DatabaseCompilerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $databases = $container->findTaggedServiceIds('dizda.cloudbackup.database');
        $dbEnabled = $container->getParameter('dizda_cloud_backup.databases');

        $chainDefinition = $container->getDefinition('dizda.cloudbackup.chain.database');

        foreach ($databases as $serviceId => $tags) {
            // Get the name of the database
            $name = explode('.', $serviceId);
            $name = end($name);

            if (!isset($dbEnabled[$name])) {
                continue;
            }

            // if the database is activated in the configuration file, we add it to the DatabaseChain
            $chainDefinition->addMethodCall('add', array(new Reference($serviceId)));
        }

    }

}
