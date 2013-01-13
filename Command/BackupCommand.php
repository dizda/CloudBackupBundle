<?php
namespace Dizda\CloudBackupBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Dizda\CloudBackupBundle\Clients\DropboxUploader;
/*use CloudApp\API as CloudApp;*/

/**
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class BackupCommand extends ContainerAwareCommand
{
    private $mongoActive;
    private $mysqlActive;
    private $output;

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
        $this->mysqlActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.mysql.active');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;




        if($this->mongoActive)
        {
            $this->output ->write('- <comment>Dumping MongoDB database...</comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mongodb');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if($this->mysqlActive)
        {
            $this->output->write('- <comment>Dumping MySQL database...</comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mysql');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        $database->compression();
        $this->output->writeln('- <info>Archive created</info> ' . $database->getArchivePath());


        $this->dropboxUploading($database->getArchivePath());

        $database->cleanUp();
        $this->output->writeln('- <info>Temporary files have been cleared</info>.');
    }


    private function dropboxUploading($archivePath)
    {
        $user       = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.user');
        $password   = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.password');
        $remotePath = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.remote_path');

        $this->output->writeln('- <comment>Uploading to Dropbox...</comment>');

        $dropbox = new DropboxUploader($user, $password);
        $dropbox->upload($archivePath, $remotePath);

        $this->output->writeln('- <info>Upload done</info>');
    }

/*    private function cloudAppUploading($archivePath)
    {
        $user     = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.cloudapp.user');
        $password = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.cloudapp.password');

        $this->output->writeln('- <comment>Uploading to CloudApp...</comment>');

        $cloudapp = new CloudApp($user, $password);
        $cloudapp->addFile($archivePath);

        $this->output->writeln('- <info>Upload done</info>');
    }*/
}