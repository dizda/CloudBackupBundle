<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Chain\ClientChain;
use Dizda\CloudBackupBundle\Chain\DatabaseChain;
use Dizda\CloudBackupBundle\Processors\ProcessorInterface;
use Dizda\CloudBackupBundle\Service\Mailer;
use Monolog\Logger;

class BackupManager
{
    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var \Dizda\CloudBackupBundle\Chain\DatabaseChain
     */
    private $databaseChain;

    /**
     * @var \Dizda\CloudBackupBundle\Chain\ClientChain
     */
    private $clientChain;

    /**
     * @var ProcessorInterface
     */
    private $processor;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Logger $logger, DatabaseChain $databaseChain, ClientChain $clientChain, Mailer $mailer)
    {
        $this->logger        = $logger;
        $this->databaseChain = $databaseChain;
        $this->clientChain   = $clientChain;
        $this->mailer        = $mailer;
    }

    /**
     *
     *
     * @throws \Exception
     */
    public function execute()
    {
        try {

            // Dump all databases
            $this->databaseChain->dump();

            $this->processor->compress();

            $wholeFile = $this->processor->getArchivePath();
            var_dump($wholeFile);

            // Transfer with all clients
            $this->clientChain->upload($this->processor->getArchivePath());

            $this->processor->cleanUp();

        } catch (\Exception $e) {

            $this->mailer->sendException($e);

            throw $e;
        }
    }

    /**
     * Set the processor to compress files
     *
     * @param ProcessorInterface $processor
     */
    public function setProcessor(ProcessorInterface $processor)
    {
        $this->processor = $processor;
    }
}
