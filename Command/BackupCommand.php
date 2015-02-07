<?php
namespace Dizda\CloudBackupBundle\Command;

use Dizda\CloudBackupBundle\Splitters\ZipSplitSplitter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;


/**
 * Run backup command
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 * @author István Manzuk <istvan.manzuk@gmail.com>
 */
class BackupCommand extends ContainerAwareCommand
{
    private $output;
    private $split;
    private $splitSize;
    private $splitStorages;
    private $databases = [];
    private $storages  = [];


    private $processors = array('tar', 'zip', '7z');
    private $clients = array('Dropbox', 'CloudApp', 'GoogleDrive', 'Gaufrette');

    protected function configure()
    {
        $this
            ->addArgument(
                'processor',
                InputArgument::OPTIONAL,
                'Which processor use? (' . implode(', ', $this->processors) .')'
            )
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
        $this->databases = $this->getContainer()->getParameter('dizda_cloud_backup.databases');
        $this->storages  = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages');

        $this->mongoActive = $this->getContainer()->hasParameter('dizda_cloud_backup.databases.mongodb.active') ? $this->getContainer()->getParameter('dizda_cloud_backup.databases.mongodb.active') : false;
        $this->mysqlActive = $this->getContainer()->hasParameter('dizda_cloud_backup.databases.mysql.active') ? $this->getContainer()->getParameter('dizda_cloud_backup.databases.mysql.active') : false;
        $this->postgresqlActive = $this->getContainer()->hasParameter('dizda_cloud_backup.databases.postgresql.active') ? $this->getContainer()->getParameter('dizda_cloud_backup.databases.postgresql.active') : false;

        $this->dropboxActive   = $this->getContainer()->hasParameter('dizda_cloud_backup.cloud_storages.dropbox.active') ? $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.active') : false;
        $this->googleDriveActive   = $this->getContainer()->hasParameter('dizda_cloud_backup.cloud_storages.google_drive.active') ? $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.google_drive.active') : false;
        $this->cloudappActive   = $this->getContainer()->hasParameter('dizda_cloud_backup.cloud_storages.cloudapp.active') ? $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.cloudapp.active') : false;
        $this->gaufretteActive   = $this->getContainer()->hasParameter('dizda_cloud_backup.cloud_storages.gaufrette.active') ? $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.gaufrette.active') : false;

        $this->split   = $this->getContainer()->hasParameter('dizda_cloud_backup.processor.options.split.enable') ? $this->getContainer()->getParameter('dizda_cloud_backup.processor.options.split.enable') : false;
        $this->splitSize   = $this->getContainer()->hasParameter('dizda_cloud_backup.processor.options.split.split_size') ? $this->getContainer()->getParameter('dizda_cloud_backup.processor.options.split.split_size') : false;
        $this->splitStorages   = $this->getContainer()->hasParameter('dizda_cloud_backup.processor.options.split.storages') ? $this->getContainer()->getParameter('dizda_cloud_backup.processor.options.split.storages') : array();
        if($this->split)
        {
            $this->splitSize = $this->getContainer()->getParameter('dizda_cloud_backup.processor.options.split.split_size');
            $this->splitStorages = $this->getContainer()->getParameter('dizda_cloud_backup.processor.options.split.storages');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        if ($input->getArgument('processor')) {
            $processorArgument = $input->getArgument('processor');
            if (!in_array($processorArgument, $this->processors)) {
                $this->output->writeln("<error>Incorrect processor $processorArgument</error>");
                $this->output->writeln("<comment>Need one of ". implode(', ', $this->processors) ."</comment>");
                return;
            }
            $this->getContainer()->setParameter('dizda_cloud_backup.processor.service', 'dizda.cloudbackup.processor.' . $processorArgument);
        }
        
        $processorType = $this->getContainer()->getParameter('dizda_cloud_backup.processor')['type'];
        $processor = $this->getContainer()->get(sprintf('dizda.cloudbackup.processor.%s', $processorType));

        if (isset($this->databases['mongodb'])) {
            $this->output->write('- <comment>Dumping MongoDB database... </comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mongodb');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if (isset($this->databases['mysql'])) {
            $this->output->write('- <comment>Dumping MySQL database... </comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mysql');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if (isset($this->databases['postgresql'])) {
            $this->output->write('- <comment>Dumping PostgreSQL database... </comment> ');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.postgresql');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if ($input->getOption('folders')){
            $this->output->write('- <comment>Copying folders... </comment> ');
            $processor->copyFolders();
            $this->output->writeln('<info>OK</info>');
        }

        $this->output->write('- <comment>Compressing archive... </comment> ');
        $processor->compress();
        $this->output->writeln('<info>OK</info>');

        $wholeFile = $processor->getArchivePath();
        $splitFiles = array();
        if($this->split)
        {
            $this->checkSplitStorages();
            $this->output->write('- <comment>Splitting archive... </comment> ');
            $split = new ZipSplitSplitter($processor->getArchivePath(), $this->splitSize);
            $split->executeSplit();
            $splitFiles = $split->getSplitFiles();
            $this->output->writeln('<info>OK</info>');
        }

        if ($this->dropboxActive) {
            if(in_array('Dropbox', $this->splitStorages)){
                $this->getContainer()->get('dizda.cloudbackup.client.dropbox')->upload($splitFiles);
            }
            else{
                $this->getContainer()->get('dizda.cloudbackup.client.dropbox')->upload($wholeFile);
            }
        }

        if ($this->googleDriveActive) {
            if(in_array('GoogleDrive', $this->splitStorages)){
                $this->getContainer()->get('dizda.cloudbackup.client.google_drive')->upload($splitFiles);
            }
            else{
                $this->getContainer()->get('dizda.cloudbackup.client.google_drive')->upload($wholeFile);
            }
        }

        if ($this->cloudappActive) {
            if(in_array('CloudApp', $this->splitStorages)){
                $this->getContainer()->get('dizda.cloudbackup.client.cloudapp')->upload($splitFiles);
            }
            else{
                $this->getContainer()->get('dizda.cloudbackup.client.cloudapp')->upload($wholeFile);
            }
        }

        if ($this->gaufretteActive) {
            $filesystemName = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages')['gaufrette']['service_name'];

            $gaufrette = $this->getContainer()->get('dizda.cloudbackup.client.gaufrette');
            $gaufrette->setFilesystem($this->getContainer()->get($filesystemName));
            if(in_array('Gaufrette', $this->splitStorages)){
                $gaufrette->upload($splitFiles);
            }
            else{
                $gaufrette->upload($wholeFile);
            }
        }

        $processor->cleanUp();
        $this->output->writeln('- <comment>Temporary files have been cleared</comment>.');
    }

    private function checkSplitStorages()
    {
        foreach($this->splitStorages as $storage)
        {
            if(!in_array($storage,$this->clients))
            {
                throw new \Exception("The storage type '$storage'' in split storages option does not exist.\nPossible options are: ".join(', ', $this->clients));
            }
        }
    }


}
