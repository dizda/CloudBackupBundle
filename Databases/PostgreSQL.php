<?php
namespace Dizda\CloudBackupBundle\Databases;

/**
 * Class MySQL
 *
 * @package Dizda\CloudBackupBundle\Databases
 * @author  István Manzuk <istvan.manzuk@gmail.com>
 */
class PostgreSQL extends BaseDatabase
{
    const DB_PATH = 'postgresql';

    private $allDatabases;
    private $database;
    private $auth = '';
    private $authPrefix = '';
    private $fileName;

    /**
     * DB Auth
     *
     * @param string $host
     * @param int    $port
     * @param string $database
     * @param string $user
     * @param string $password
     * @param string $basePath
     */
    public function __construct($host, $port, $database, $user, $password, $basePath)
    {
        parent::__construct($basePath);

        $this->database   = $database;
        $this->auth       = '';
        $this->authPrefix = '';
        $this->fileName   = $this->database . '.sql';

        if ($password) {
            $this->authPrefix = sprintf('export PGPASSWORD="%s" && ', $password);
        }
        if ($user) {
            $this->auth = sprintf('--username "%s" ', $user);
        }
        //TODO: pg_dump options support
        $this->auth .= sprintf('--host %s --port %d --format plain --encoding UTF8', $host, $port);

    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $this->preparePath();
        $this->execute($this->getCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        //TODO: pg_dumpall support
        return sprintf('%spg_dump %s "%s" > "%s"',
            $this->authPrefix,
            $this->auth,
            $this->database,
            $this->dataPath . $this->fileName);
    }

}