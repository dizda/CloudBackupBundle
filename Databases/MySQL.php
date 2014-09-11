<?php
namespace Dizda\CloudBackupBundle\Databases;

/**
 * Class MySQL
 *
 * @package Dizda\CloudBackupBundle\Databases
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class MySQL extends BaseDatabase
{
    const DB_PATH = 'mysql';

    private $allDatabases;
    private $database;
    private $auth = '';
    private $fileName;

    /**
     * DB Auth
     *
     * @param bool   $allDatabases
     * @param string $host
     * @param int    $port
     * @param string $database
     * @param string $user
     * @param string $password
     * @param string $basePath
     */
    public function __construct($allDatabases, $host, $port, $database, $user, $password, $basePath)
    {
        parent::__construct($basePath);

        $this->allDatabases = $allDatabases;
        $this->database     = $database;
        $this->auth         = '';

        if ($this->allDatabases) {
            $this->database = '--all-databases';
            $this->fileName = 'all-databases.sql';
        } else {
            $this->fileName = $this->database . '.sql';
        }

        /* if user is set, we add authentification */
        if ($user) {
            $this->auth = sprintf('-u%s', $user);

            if ($password) {
                $this->auth = sprintf("--host='%s' --port='%d' --user='%s' --password='%s'", $host, $port, $user, $password);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $this->execute($this->getCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        return sprintf('mysqldump %s %s > %s',
            $this->auth,
            $this->database,
            $this->dataPath . $this->fileName);
    }

}
