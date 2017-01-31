<?php
namespace Dizda\CloudBackupBundle\Listener;

use Dizda\CloudBackupBundle\Event\RestoreEvent;
use Psr\Log\LoggerInterface;

class LogRestoreCompletedListener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param RestoreEvent $event
     */
    public function whenRestoreIsCompleted(RestoreEvent $event)
    {
        $this->logger->info(sprintf('[dizda-backup] Restoring %s is completed', $event->getFile()->getFilename()));
    }
}
