<?php
namespace Dizda\CloudBackupBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Dizda\CloudBackupBundle\Client\DropboxUploader;

/**
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class BackupCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('dizda:cloud:backup')
            ->setDescription('Upload backup to the cloud service\'s')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {



        /*$tmpfname = tempnam("/tmp");

        $handle = fopen($tmpfname, "w");
        fwrite($handle, "Écriture dans le fichier temporaire");
        fclose($handle);*/


        $dropbox = new DropboxUploader('dizzda@gmail.com', '5335JDDJdX');
        $dropbox->upload($tmpfname, '/Backups/bankmanager/', 'test.txt');



        $output->writeln('All ok.');

    }
}