<?php

namespace Dizda\CloudBackupBundle;

use Dizda\CloudBackupBundle\DependencyInjection\Compiler\ClientCompilerPass;
use Dizda\CloudBackupBundle\DependencyInjection\Compiler\DatabaseCompilerPass;
use Dizda\CloudBackupBundle\DependencyInjection\Compiler\ProcessorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DizdaCloudBackupBundle extends Bundle
{

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DatabaseCompilerPass());
        $container->addCompilerPass(new ClientCompilerPass());
        $container->addCompilerPass(new ProcessorCompilerPass());
    }
}
