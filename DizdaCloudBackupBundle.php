<?php

namespace Dizda\CloudBackupBundle;

use Dizda\CloudBackupBundle\DependencyInjection\Compiler\TaggedServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DizdaCloudBackupBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TaggedServicesPass());
    }
}
