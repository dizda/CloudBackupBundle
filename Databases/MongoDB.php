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
     *     "database"     = @DI\Inject("%dizda_cloud_backup.databases.mongodb.database%"),
     *     "user"         = @DI\Inject("%dizda_cloud_backup.databases.mongodb.db_user%"),
     *     "password"     = @DI\Inject("%dizda_cloud_backup.databases.mongodb.db_password%")
     * })
     */
    public function __construct($allDatabases, $database, $user, $password)
    {
        parent::__construct();

        $this->allDatabases = $allDatabases;
        $this->database     = $database;
        $this->auth         = '';

        if($this->allDatabases)
        {
            $this->database = '';
        }else{
            $this->database = sprintf('--db %s', $this->database);
        }

        /* if user is set, we add authentification */
        if($user)
        {
            $this->auth = sprintf('-u %s', $user, $password);

            if($password) $this->auth = sprintf('-u %s -p %s', $user, $password);
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