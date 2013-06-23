<?php
namespace Dizda\CloudBackupBundle\Databases;


/**
 * Service("dizda.cloudbackup.database.mysql");
 */
class MySQL extends BaseDatabase
{
    const DB_PATH = 'mysql';

    private $allDatabases;
    private $database;
    private $auth = '';
    private $fileName;


    /**
     * InjectParams({
     *     "allDatabases" = Inject("%dizda_cloud_backup.databases.mysql.all_databases%"),
     *     "host"         = DI\Inject("%dizda_cloud_backup.databases.mysql.host%"),
     *     "port"         = DI\Inject("%dizda_cloud_backup.databases.mysql.port%"),
     *     "database"     = DI\Inject("%dizda_cloud_backup.databases.mysql.database%"),
     *     "user"         = DI\Inject("%dizda_cloud_backup.databases.mysql.db_user%"),
     *     "password"     = DI\Inject("%dizda_cloud_backup.databases.mysql.db_password%")
     * })
     */
    public function __construct($allDatabases, $host, $port, $database, $user, $password)
    {
        parent::__construct();

        $this->allDatabases = $allDatabases;
        $this->database     = $database;
        $this->auth         = '';

        if($this->allDatabases)
        {
            $this->database = '--all-databases';
            $this->fileName = 'all-databases.sql';
        }else{
            $this->fileName = $this->database . '.sql';
        }

        /* if user is set, we add authentification */
        if($user)
        {
            $this->auth = sprintf('-u%s', $user);

            if($password) $this->auth = sprintf('--host=%s --port=%d --user=%s --password=%s', $host, $port, $user, $password);
        }

    }


    public function dump()
    {
        parent::prepare();

        $cmd    = sprintf('mysqldump %s %s > %s',
                           $this->auth,
                           $this->database,
                           $this->dataPath . $this->fileName);

        $this->execute($cmd);
    }

}