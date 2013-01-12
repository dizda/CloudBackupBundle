<?php
namespace Dizda\CloudBackupBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Dizda\CloudBackupBundle\Clients\DropboxUploader;

/**
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class BackupCommand extends ContainerAwareCommand
{
    private $mongoActive;

    protected function configure()
    {
        $this
            ->setName('dizda:backup:start')
            ->setDescription('Upload a backup of your database to cloud service\'s')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->mongoActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.mongodb.active');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user     = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.user');
        $password = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.password');


        if($this->mongoActive)
        {
            $mongodb = $this->getContainer()->get('dizda.cloudbackup.database.mongodb');
            $mongodb->dump();
            $mongodb->compression();
        }



        $output->writeln('- <info>Archive created</info> ' . $mongodb->getArchivePath());
        $output->writeln('- <comment>Uploading to Dropbox...</comment>');

        $dropbox = new DropboxUploader($user, $password);
        $dropbox->upload($mongodb->getArchivePath(), '/Backups/bankmanager/');

        $output->writeln('- <info>Upload done</info>');

        $mongodb->cleanUp();

        $output->writeln('- <info>Temporary files have been cleared</info>.');

    }
}