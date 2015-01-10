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
     * @param array  $params
     * @param string $basePath
     */
    public function __construct($params, $basePath)
    {
        parent::__construct($basePath);

        $params = $params['mysql'];
        $this->allDatabases = $params['all_databases'];
        $this->database     = $params['database'];
        $this->auth         = '';

        if ($this->allDatabases) {
            $this->database = '--all-databases';
            $this->fileName = 'all-databases.sql';
        } else {
            $this->fileName = $this->database . '.sql';
        }

        /* if user is set, we add authentification */
        if ($params['db_user']) {
            $this->auth = sprintf('-u%s', $params['db_user']);

            if ($params['db_password']) {
                $this->auth = sprintf("--host='%s' --port='%d' --user='%s' --password='%s'", $params['db_host'], $params['db_port'], $params['db_user'], $params['db_password']);
            }
        }
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
        return sprintf('mysqldump %s %s > %s',
            $this->auth,
            $this->database,
            $this->dataPath . $this->fileName);
    }

}
