<?php
namespace Dizda\CloudBackupBundle\Databases;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Class BaseDatabase
 *
 * @package Dizda\CloudBackupBundle\Databases
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
abstract class BaseDatabase
{
    const DB_PATH = '';

    protected $dataPath;
    protected $database;
    protected $filesystem;

    /**
     * Get SF2 Filesystem
     *
     * @param string $basePath
     * @param string $databases
     */
    public function __construct($basePath, $databases)
    {
        $this->dataPath = $basePath . static::DB_PATH . '/';
        $this->database = $databases;
        
        $this->filesystem = new Filesystem();
    }

    /**
     * Handle process error on fails
     *
     * @param string $command
     *
     * @throws \RuntimeException
     */
    protected function execute($command)
    {
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }
    
    /**
     * Prepare path for dump file
     */
    protected function preparePath()
    {
        $this->filesystem->mkdir($this->dataPath);
    }

    /**
     * Migration procedure for each databases type
     *
     * @return mixed
     */
    public function dump()
    {
        $this->preparePath();
        
        $databases = $this->database;
        
        foreach ($databases as $database) {
            $this->database = $database;
            $this->execute($this->getCommand());
        }
    }

    /**
     * Get file extension
     * 
     * @return string
     */
    public function getExtension()
    {
        return '.sql';
    }
    
    /**
     * Get command to execute dump
     *
     * @return string
     */
    abstract public function getCommand();

}
