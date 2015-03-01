<?php
namespace Dizda\CloudBackupBundle\Database;

/**
 * Class MySQL.
 *
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
     * DB Auth.
     *
     * @param array  $params
     * @param string $basePath
     */
    public function __construct($params, $basePath)
    {
        parent::__construct($basePath);

        $params           = $params['postgresql'];
        $this->database   = $params['database'];
        $this->auth       = '';
        $this->authPrefix = '';
        $this->fileName   = $this->database.'.sql';

        if ($params['db_password']) {
            $this->authPrefix = sprintf('export PGPASSWORD="%s" && ', $params['db_password']);
        }
        if ($params['db_user']) {
            $this->auth = sprintf('--username "%s" ', $params['db_user']);
        }

        //TODO: pg_dump options support
        $this->auth .= sprintf('--host %s --port %d --format plain --encoding UTF8', $params['db_host'], $params['db_port']);
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
        //TODO: pg_dumpall support
        return sprintf('%spg_dump %s "%s" > "%s"',
            $this->authPrefix,
            $this->auth,
            $this->database,
            $this->dataPath.$this->fileName);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'PostgreSQL';
    }
}
