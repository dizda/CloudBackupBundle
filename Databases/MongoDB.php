<?php
namespace Dizda\CloudBackupBundle\Databases;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("dizda.cloudbackup.database.mongodb");
 */
class MongoDB extends BaseDatabase
{
    const DB_PATH = 'mongo';

    private $allDatabases;
    private $database;
    private $auth = '';


    /**
     * @DI\InjectParams({
     *     "allDatabases" = @DI\Inject("%dizda_cloud_backup.databases.mongodb.all_databases%"),
     *     "host"         = @DI\Inject("%dizda_cloud_backup.databases.mongodb.host%"),
     *     "port"         = @DI\Inject("%dizda_cloud_backup.databases.mongodb.port%"),
     *     "database"     = @DI\Inject("%dizda_cloud_backup.databases.mongodb.database%"),
     *     "user"         = @DI\Inject("%dizda_cloud_backup.databases.mongodb.db_user%"),
     *     "password"     = @DI\Inject("%dizda_cloud_backup.databases.mongodb.db_password%")
     * })
     */
    public function __construct($allDatabases, $host, $port, $database, $user, $password)
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


    public function dump()
    {
        parent::prepare();

        $cmd    = sprintf('mongodump %s %s --out %s',
                           $this->auth,
                           $this->database,
                           $this->dataPath);

        exec($cmd);
    }

}