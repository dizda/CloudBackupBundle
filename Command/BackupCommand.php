<?php
namespace Dizda\CloudBackupBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;



/**
 * Run backup command
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 * @author István Manzuk <istvan.manzuk@gmail.com>
 */
class BackupCommand extends ContainerAwareCommand
{
    private $mongoActive;
    private $mysqlActive;
    private $dropboxActive;
    private $cloudappActive;
    private $gaufretteActive;
    private $output;

    protected function configure()
    {
        $this
            ->addOption(
                'folders',
                'F',
                 InputOption::VALUE_NONE,
                'Do you want to export also folders?'
                )
            ->setName('dizda:backup:start')
            ->setDescription('Upload a backup of your database to cloud services (use -F option for backup folders)');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->mongoActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.mongodb.active');
        $this->mysqlActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.mysql.active');
        $this->postgresqlActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.postgresql.active');

        $this->dropboxActive   = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.active');
        $this->cloudappActive  = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.cloudapp.active');
        $this->gaufretteActive = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.gaufrette.active');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        if ($this->mongoActive) {
            $this->output ->write('- <comment>Dumping MongoDB database...</comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mongodb');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if ($this->mysqlActive) {
            $this->output->write('- <comment>Dumping MySQL database...</comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mysql');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if ($this->postgresqlActive) {
            $this->output->write('- <comment>Dumping PostgreSQL database...</comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.postgresql');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if($input->getOption('folders')){
            $this->output->write('- <comment>Copying folders...</comment>');
            $database->copyFolders();
            $this->output->writeln('<info>OK</info>');
        }
        $database->compression();
        $this->output->writeln('- <info>Archive created</info> ' . $database->getArchivePath());


        if ($this->dropboxActive) {
            $this->getContainer()->get('dizda.cloudbackup.client.dropbox')->upload($database->getArchivePath());
        }

        if ($this->cloudappActive) {
            $this->getContainer()->get('dizda.cloudbackup.client.cloudapp')->upload($database->getArchivePath());
        }

        if ($this->gaufretteActive) {
            $filesystemName = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.gaufrette.service_name');

            $gaufrette = $this->getContainer()->get('dizda.cloudbackup.client.gaufrette');
            $gaufrette->setFilesystem($this->getContainer()->get($filesystemName));
            $gaufrette->upload($database->getArchivePath());
        }


        $database->cleanUp();
        $this->output->writeln('- <info>Temporary files have been cleared</info>.');
    }


}
