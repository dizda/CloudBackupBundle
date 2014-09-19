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
    private $auth = '';

    /**
     * DB Auth
     *
     * @param bool   $allDatabases
     * @param string $host
     * @param int    $port
     * @param array  $databases
     * @param string $user
     * @param string $password
     * @param string $basePath
     */
    public function __construct($allDatabases, $host, $port, $databases, $user, $password, $basePath)
    {
        if ($this->allDatabases) {
            $databases = array('');
        } else {
            foreach ($databases as &$database) {
                $database = sprintf('--db %s', $this->database);
            }
        }

        parent::__construct($basePath, $databases);

        $this->allDatabases = $allDatabases;
        $this->auth         = '';

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
    public function getCommand()
    {
        return sprintf('mongodump %s %s --out %s',
            $this->auth,
            $this->database,
            $this->dataPath);
    }
}