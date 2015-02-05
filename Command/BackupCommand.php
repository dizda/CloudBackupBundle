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
    private $mongoActive;
    private $mysqlActive;
    private $postgresqlActive;
    private $dropboxActive;
    private $googleDriveActive;
    private $cloudappActive;
    private $gaufretteActive;
    private $output;
    private $split;
    private $splitSize;
    private $splitStorages;

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
        $this->mongoActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.mongodb.active');
        $this->mysqlActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.mysql.active');
        $this->postgresqlActive = $this->getContainer()->getParameter('dizda_cloud_backup.databases.postgresql.active');

        $this->dropboxActive   = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.dropbox.active');
        $this->googleDriveActive   = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.google_drive.active');
        $this->cloudappActive  = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.cloudapp.active');
        $this->gaufretteActive = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.gaufrette.active');

        $this->split = $this->getContainer()->getParameter('dizda_cloud_backup.processor.options.split.enable');
        $this->splitStorages = array();
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
        
        $processorType = $this->getContainer()->getParameter('dizda_cloud_backup.processor.service');
        $processor = $this->getContainer()->get($processorType);
        
        if ($this->mongoActive) {
            $this->output->write('- <comment>Dumping MongoDB database... </comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mongodb');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if ($this->mysqlActive) {
            $this->output->write('- <comment>Dumping MySQL database... </comment>');

            $database = $this->getContainer()->get('dizda.cloudbackup.database.mysql');
            $database->dump();

            $this->output->writeln('<info>OK</info>');
        }

        if ($this->postgresqlActive) {
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
            $filesystemName = $this->getContainer()->getParameter('dizda_cloud_backup.cloud_storages.gaufrette.service_name');

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
