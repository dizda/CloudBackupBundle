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
    protected $filesystem;

    /**
     * Get SF2 Filesystem
     *
     * @param string $basePath
     */
    public function __construct($basePath)
    {
        $this->dataPath = $basePath . static::DB_PATH . '/';
        
        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->dataPath);
    }

    /**
     * Migration procedure for each databases type
     *
     * @return mixed
     */
    abstract public function dump();

    /**
     * Get command to execute dump
     *
     * @return string
     */
    abstract public function getCommand();

}
