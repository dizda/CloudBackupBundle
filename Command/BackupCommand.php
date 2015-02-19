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
    private $processors = array('tar', 'zip', '7z');

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if (!$this->getContainer()->get('dizda.cloudbackup.manager.backup')->execute()) {
            $output->writeln('<error>Something went terribly wrong. We could not create a backup/error>');

            return 1; //error
        }

        $output->writeln('<info>Backup complete.</info>');

    }
}
