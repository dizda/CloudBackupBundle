<?php
namespace Dizda\CloudBackupBundle\Database;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class BaseDatabase.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
abstract class BaseDatabase implements DatabaseInterface
{
    const DB_PATH = '';

    protected $dataPath;
    protected $filesystem;
    protected $timeout;

    /**
     * Get SF2 Filesystem.
     *
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->dataPath = $basePath.static::DB_PATH.'/';
        $this->filesystem = new Filesystem();
        $this->timeout = 300;
    }

    /**
     * Handle process error on fails.
     *
     * @param string $command
     *
     * @throws \RuntimeException
     */
    protected function execute($command)
    {
        $process = new Process($command, null, null, null, $this->timeout);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * Prepare path for dump file.
     */
    protected function preparePath()
    {
        $this->filesystem->mkdir($this->dataPath);
    }

    /**
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }
}
