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
    private $auth = '';
    private $fileName;

    /**
     * DB Auth
     *
     * @param bool   $allDatabases
     * @param string $host
     * @param int    $port
     * @param string $databases
     * @param string $user
     * @param string $password
     * @param string $basePath
     */
    public function __construct($allDatabases, $host, $port, $databases, $user, $password, $basePath)
    {
        parent::__construct($basePath, $databases);

        $this->allDatabases = $allDatabases;
        $this->auth         = '';

        if ($this->allDatabases) {
            $this->database = array('--all-databases');
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
    public function getCommand()
    {
        return sprintf('mysqldump %s %s > %s',
            $this->auth,
            $this->database,
            $this->dataPath . ltrim($this->database, '-') . $this->getExtension());
    }

}
