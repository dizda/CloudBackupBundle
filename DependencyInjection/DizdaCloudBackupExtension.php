<?php

namespace Dizda\CloudBackupBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DizdaCloudBackupExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        /* Config output file */
        $container->setParameter('dizda_cloud_backup.root_folder', $container->getParameter('kernel.root_dir') . '/../');
        $container->setParameter('dizda_cloud_backup.output_folder', $container->getParameter('kernel.cache_dir') . '/backup/');
        $container->setParameter('dizda_cloud_backup.output_file_prefix', $config['output_file_prefix']);
        $container->setParameter('dizda_cloud_backup.folders', $config['folders']);
        
        /* Config processor */
        $container->setParameter('dizda_cloud_backup.processor.service', 'dizda.cloudbackup.processor.' . $config['processor']['type']);
        $container->setParameter('dizda_cloud_backup.processor.dateformat', $config['processor']['date_format']);
        $container->setParameter('dizda_cloud_backup.processor.options', $config['processor']['options']);
        
        /* Config dropbox */
        if (isset($config['cloud_storages']['dropbox'])) {
            $container->setParameter('dizda_cloud_backup.cloud_storages.dropbox.active',      true);
            $container->setParameter('dizda_cloud_backup.cloud_storages.dropbox.user',        $config['cloud_storages']['dropbox']['user']);
            $container->setParameter('dizda_cloud_backup.cloud_storages.dropbox.password',    $config['cloud_storages']['dropbox']['password']);
            $container->setParameter('dizda_cloud_backup.cloud_storages.dropbox.remote_path', $config['cloud_storages']['dropbox']['remote_path']);
        }else{
            $this->setDefaultsParameters($container, array( 'dizda_cloud_backup.cloud_storages.dropbox.active',
                                                            'dizda_cloud_backup.cloud_storages.dropbox.user',
                                                            'dizda_cloud_backup.cloud_storages.dropbox.password',
                                                            'dizda_cloud_backup.cloud_storages.dropbox.remote_path'));
        }

        /* Config google drive */
        if (isset($config['cloud_storages']['google_drive'])) {
            if (!class_exists('Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider')) {
                throw new \LogicException('DizdaCloudBundle: You need to install and configure Happyr/GoogleSiteAuthenticatorBundle to be able to use Google Drive as remote storage.');
            }

            $container->setParameter('dizda_cloud_backup.cloud_storages.google_drive.active', true);
            $dev=$container->getDefinition('dizda.cloudbackup.client.google_drive');
            $dev->setPublic(true)
                ->replaceArgument(0, new Reference('happyr.google_site_authenticator.client_provider'))
                ->replaceArgument(1, $config['cloud_storages']['google_drive']['token_name'])
                ->replaceArgument(2, $config['cloud_storages']['google_drive']['remote_path']);
        } else {
            $this->setDefaultsParameters($container, array( 'dizda_cloud_backup.cloud_storages.google_drive.active'));
        }

        /* Config CloudApp */
        if (isset($config['cloud_storages']['cloudapp'])) {
            $container->setParameter('dizda_cloud_backup.cloud_storages.cloudapp.active',      true);
            $container->setParameter('dizda_cloud_backup.cloud_storages.cloudapp.user',        $config['cloud_storages']['cloudapp']['user']);
            $container->setParameter('dizda_cloud_backup.cloud_storages.cloudapp.password',    $config['cloud_storages']['cloudapp']['password']);
        }else{
            $this->setDefaultsParameters($container, array( 'dizda_cloud_backup.cloud_storages.cloudapp.active',
                                                            'dizda_cloud_backup.cloud_storages.cloudapp.user',
                                                            'dizda_cloud_backup.cloud_storages.cloudapp.password' ));
        }


        /* Config Gaufrette */
        if (isset($config['cloud_storages']['gaufrette'])) {
            $container->setParameter('dizda_cloud_backup.cloud_storages.gaufrette.active',      true);
            $container->setParameter('dizda_cloud_backup.cloud_storages.gaufrette.service_name',$config['cloud_storages']['gaufrette']['service_name']);
        }else{
            $this->setDefaultsParameters($container, array( 'dizda_cloud_backup.cloud_storages.gaufrette.active',
                                                            'dizda_cloud_backup.cloud_storages.gaufrette.service_name'  ));
        }



        if(isset($config['databases']['mongodb']))
        {
            $container->setParameter('dizda_cloud_backup.databases.mongodb.active',         true);
            $container->setParameter('dizda_cloud_backup.databases.mongodb.all_databases',  $config['databases']['mongodb']['all_databases']);
            $container->setParameter('dizda_cloud_backup.databases.mongodb.database',       $config['databases']['mongodb']['database']);
            $container->setParameter('dizda_cloud_backup.databases.mongodb.host',           $config['databases']['mongodb']['db_host']);
            $container->setParameter('dizda_cloud_backup.databases.mongodb.port',           $config['databases']['mongodb']['db_port']);
            $container->setParameter('dizda_cloud_backup.databases.mongodb.db_user',        $config['databases']['mongodb']['db_user']);
            $container->setParameter('dizda_cloud_backup.databases.mongodb.db_password',    $config['databases']['mongodb']['db_password']);
        }else{
            /* If mongodb is not specified in config, we set all parameters to false, and it will be not used */
            $this->setDefaultsParameters($container, array( 'dizda_cloud_backup.databases.mongodb.active',
                                                            'dizda_cloud_backup.databases.mongodb.all_databases',
                                                            'dizda_cloud_backup.databases.mongodb.database',
                                                            'dizda_cloud_backup.databases.mongodb.host',
                                                            'dizda_cloud_backup.databases.mongodb.port',
                                                            'dizda_cloud_backup.databases.mongodb.db_user',
                                                            'dizda_cloud_backup.databases.mongodb.db_password',
            ));
        }

        if(isset($config['databases']['mysql']))
        {
            $container->setParameter('dizda_cloud_backup.databases.mysql.active',        true);
            $container->setParameter('dizda_cloud_backup.databases.mysql.all_databases', $config['databases']['mysql']['all_databases']);
            $container->setParameter('dizda_cloud_backup.databases.mysql.database',      $config['databases']['mysql']['database'] ?: $container->getParameter('database_name'));
            
            if($config['databases']['mysql']['db_host'] !== null && $config['databases']['mysql']['db_user'] !== null)
            {
                $container->setParameter('dizda_cloud_backup.databases.mysql.host',        $config['databases']['mysql']['db_host']);
                $container->setParameter('dizda_cloud_backup.databases.mysql.port',        $config['databases']['mysql']['db_port']);
                $container->setParameter('dizda_cloud_backup.databases.mysql.db_user',     $config['databases']['mysql']['db_user']);
                $container->setParameter('dizda_cloud_backup.databases.mysql.db_password', $config['databases']['mysql']['db_password']);

                // if port not setted in parameters.yml, we set default port
                if ($container->getParameter('dizda_cloud_backup.databases.mysql.port') == null) {
                    $container->setParameter('dizda_cloud_backup.databases.mysql.port', 3306);
                }
            }else{ /* if mysql config is not set, we taking from the parameters.yml values */
                $container->setParameter('dizda_cloud_backup.databases.mysql.host',        $container->getParameter('database_host'));

                if($container->getParameter('database_port') === null)
                {
                    $container->setParameter('dizda_cloud_backup.databases.mysql.port',    $config['databases']['mysql']['db_port']);
                }else{
                    $container->setParameter('dizda_cloud_backup.databases.mysql.port',    $container->getParameter('database_port'));
                }

                $container->setParameter('dizda_cloud_backup.databases.mysql.db_user',     $container->getParameter('database_user'));
                $container->setParameter('dizda_cloud_backup.databases.mysql.db_password', $container->getParameter('database_password'));
            }


        }else{
            /* If mysql is not specified in config, we set all parameters to false, and it will be not used */
            $this->setDefaultsParameters($container, array( 'dizda_cloud_backup.databases.mysql.active',
                                                            'dizda_cloud_backup.databases.mysql.all_databases',
                                                            'dizda_cloud_backup.databases.mysql.database',
                                                            'dizda_cloud_backup.databases.mysql.host',
                                                            'dizda_cloud_backup.databases.mysql.port',
                                                            'dizda_cloud_backup.databases.mysql.db_user',
                                                            'dizda_cloud_backup.databases.mysql.db_password',
            ));
        }

        if (isset($config['databases']['postgresql'])) {
            $container->setParameter('dizda_cloud_backup.databases.postgresql.active', true);
            $container->setParameter('dizda_cloud_backup.databases.postgresql.all_databases', false); //'all databases' option not implemented
            $container->setParameter('dizda_cloud_backup.databases.postgresql.database', $config['databases']['postgresql']['database'] ?: $container->getParameter('database_name'));

            if ($config['databases']['postgresql']['db_host'] !== null && $config['databases']['postgresql']['db_user'] !== null) {
                $container->setParameter('dizda_cloud_backup.databases.postgresql.host',        $config['databases']['postgresql']['db_host']);
                $container->setParameter('dizda_cloud_backup.databases.postgresql.port',        $config['databases']['postgresql']['db_port']);
                $container->setParameter('dizda_cloud_backup.databases.postgresql.db_user',     $config['databases']['postgresql']['db_user']);
                $container->setParameter('dizda_cloud_backup.databases.postgresql.db_password', $config['databases']['postgresql']['db_password']);

                // if port not setted in parameters.yml, we set default port
                if ($container->getParameter('dizda_cloud_backup.databases.postgresql.port') == null) {
                    $container->setParameter('dizda_cloud_backup.databases.postgresql.port', 5421);
                }
            } else { /* if postgresql config is not set, we taking from the parameters.yml values */
                $container->setParameter('dizda_cloud_backup.databases.postgresql.host',     $container->getParameter('database_host'));

                if($container->getParameter('database_port') === null) {
                    $container->setParameter('dizda_cloud_backup.databases.postgresql.port', $config['databases']['postgresql']['db_port']);
                } else {
                    $container->setParameter('dizda_cloud_backup.databases.postgresql.port', $container->getParameter('database_port'));
                }

                $container->setParameter('dizda_cloud_backup.databases.postgresql.db_user',     $container->getParameter('database_user'));
                $container->setParameter('dizda_cloud_backup.databases.postgresql.db_password', $container->getParameter('database_password'));
            }

        } else {
            /* If postgresql is not specified in config, we set all parameters to false, and it will be not used */
            $this->setDefaultsParameters($container, array( 
                'dizda_cloud_backup.databases.postgresql.active',
                'dizda_cloud_backup.databases.postgresql.all_databases',
                'dizda_cloud_backup.databases.postgresql.database',
                'dizda_cloud_backup.databases.postgresql.host',
                'dizda_cloud_backup.databases.postgresql.port',
                'dizda_cloud_backup.databases.postgresql.db_user',
                'dizda_cloud_backup.databases.postgresql.db_password',
            ));
        }

    }

    /**
     * Setting all params to false
     *
     * @param $container
     * @param array $parameters vars to set to false
     */
    private function setDefaultsParameters($container, array $parameters)
    {
        foreach($parameters as $parameter)
        {
            $container->setParameter($parameter, false);
        }
    }
}
