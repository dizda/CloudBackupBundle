<?php

namespace Dizda\CloudBackupBundle\Command;

use Dizda\CloudBackupBundle\Manager\RestoreManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RestoreCommand extends Command
{
    const RETURN_STATUS_SUCCESS = 0;
    const RETURN_STATUS_NOT_AVAILABLE = 1;
    const RETURN_STATUS_MISSING_FORCE_FLAG = 2;
    const RETURN_STATUS_EXCEPTION_OCCURRED = 3;

    /**
     * @var bool
     */
    protected $doRestore;

    /**
     * @var RestoreManager
     */
    protected $restoreManager;

    /**
     * @param boolean $doRestore
     * @param RestoreManager $restoreManager
     */
    public function __construct($doRestore, RestoreManager $restoreManager)
    {
        $this->doRestore = $doRestore;
        $this->restoreManager = $restoreManager;

        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('dizda:backup:restore')
            ->setDescription('Download latest backup, uncompress and restore.')
            ->addOption('force', null, InputOption::VALUE_NONE)
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->doRestore) {
            $output->writeln('<error>Restore is not available. Enable by setting dizda_cloud_backup.restore: true in config.yml.</error>');

            return self::RETURN_STATUS_NOT_AVAILABLE;
        }

        if (!$input->getOption('force')) {
            $output->writeln('<error>Run command with --force to execute.</error>');

            return self::RETURN_STATUS_MISSING_FORCE_FLAG;
        }

        $output->writeln('Restoring backup started');

        if ($this->restoreManager->execute()) {
            $output->writeln('Restoring backup completed');

            return self::RETURN_STATUS_SUCCESS;
        } else {
            $output->writeln('<error>Something went wrong</error>');

            return self::RETURN_STATUS_EXCEPTION_OCCURRED;
        }
    }
}
