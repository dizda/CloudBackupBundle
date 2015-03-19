<?php
namespace Dizda\CloudBackupBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Run backup command.
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 * @author István Manzuk <istvan.manzuk@gmail.com>
 */
class BackupCommand extends ContainerAwareCommand
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('dizda:backup:start')
            ->setDescription('Upload a backup of your database to your cloud services.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getContainer()->get('dizda.cloudbackup.manager.backup')->execute()) {
            $output->writeln('<error>Something went terribly wrong. We could not create a backup. Read your log files to see what caused this error.</error>');

            return 1; //error
        }

        $output->writeln('<info>Backup complete.</info>');
    }
}
