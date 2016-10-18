<?php
namespace Dizda\CloudBackupBundle\Listener;

use Dizda\CloudBackupBundle\Event\RestoreEvent;
use Dizda\CloudBackupBundle\Event\RestoreFailedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class CleanRestoreWorkspaceListener
{
    /**
     * @var string
     */
    private $restoreFolder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param string $restoreFolder
     * @param Filesystem $filesystem
     */
    public function __construct($restoreFolder, Filesystem $filesystem)
    {
        $this->restoreFolder = $restoreFolder;
        $this->filesystem = $filesystem;
    }

    /**
     * @param RestoreEvent $event
     */
    public function whenRestoreIsCompleted(RestoreEvent $event)
    {
        $this->clean();
    }

    /**
     * @param RestoreFailedEvent $event
     */
    public function whenRestoreIsFailed(RestoreFailedEvent $event)
    {
        $this->clean();
    }

    private function clean()
    {
        $this->filesystem->remove($this->restoreFolder);
    }
}
