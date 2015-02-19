<?php

namespace Dizda\CloudBackupBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Tobias Nyholm
 */
class ProcessorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $processors = $container->findTaggedServiceIds('dizda.cloudbackup.processor');
        $options = $container->getParameter('dizda_cloud_backup.processor.options');

        foreach ($processors as $serviceId => $tags) {
            $container->getDefinition($serviceId)->addMethodCall('addOptions', array($options));
        }

    }

}
