<?php
namespace Dizda\CloudBackupBundle\Databases;

/**
 * Class MongoDB
 *
 * @package Dizda\CloudBackupBundle\Databases
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class MongoDB extends BaseDatabase
{
    const DB_PATH = 'mongo';

    private $allDatabases;
    private $database;
    private $auth = '';

    /**
     * DB Auth
     *
     * @param bool   $allDatabases
     * @param string $host
     * @param int    $port
     * @param string $database
     * @param string $user
     * @param string $password
     */
    public function __construct($allDatabases, $host, $port = 27017, $database, $user, $password)
    {
        parent::__construct();

        $this->allDatabases = $allDatabases;
        $this->database     = $database;
        $this->auth         = '';

        if ($this->allDatabases) {
            $this->database = '';
        } else {
            $this->database = sprintf('--db %s', $this->database);
        }

        /* Setting hostname & port */
        $this->auth = sprintf('-h %s --port %d', $host, $port);

        /* if user is set, we add authentification */
        if ($user) {
            $this->auth = sprintf('-h %s --port %d -u %s', $host, $port, $user);

            if ($password) {
                $this->auth = sprintf('-h %s --port %d -u %s -p %s', $host, $port, $user, $password);
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        parent::prepare();

        $this->execute($this->getCommand());
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        return sprintf('mongodump %s %s --out %s',
            $this->auth,
            $this->database,
            $this->dataPath);
    }
}