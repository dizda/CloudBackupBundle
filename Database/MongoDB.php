<?php
namespace Dizda\CloudBackupBundle\Database;

/**
 * Class MongoDB.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class MongoDB extends BaseDatabase
{
    const DB_PATH = 'mongo';

    private $database;
    private $auth = '';

    /**
     * DB Auth.
     *
     * @param array  $params
     * @param string $basePath
     */
    public function __construct($params, $basePath)
    {
        parent::__construct($basePath);

        $params         = $params['mongodb'];
        $this->database     = $params['database'];
        $this->auth         = '';

        if ($params['all_databases']) {
            $this->database = '';
        } else {
            $this->database = sprintf('--db %s', $this->database);
        }

        /* Setting hostname & port */
        $this->auth = sprintf('-h %s --port %d', $params['db_host'], $params['db_port']);

        /* if user is set, we add authentification */
        if ($params['db_user']) {
            $this->auth = sprintf('-h %s --port %d -u %s', $params['db_host'], $params['db_port'], $params['db_user']);

            if ($params['db_password']) {
                $this->auth = sprintf('-h %s --port %d -u %s -p %s', $params['db_host'], $params['db_port'], $params['db_user'], $params['db_password']);
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
    protected function getCommand()
    {
        return sprintf('mongodump %s %s --out %s',
            $this->auth,
            $this->database,
            $this->dataPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'MongoDB';
    }
}
