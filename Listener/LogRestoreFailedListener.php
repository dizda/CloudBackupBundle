<?php
namespace Dizda\CloudBackupBundle\Listener;

use Dizda\CloudBackupBundle\Event\RestoreFailedEvent;
use Psr\Log\LoggerInterface;

class LogRestoreFailedListener
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
     * @param RestoreFailedEvent $event
     */
    public function whenRestoreIsFailed(RestoreFailedEvent $event)
    {
        $this->logger->critical('[dizda-backup] Restoring database failed', ['exception' => $event->getException()]);
    }
}
