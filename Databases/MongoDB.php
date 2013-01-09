<?php
namespace Dizda\CloudBackupBundle\Databases;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("dizda.cloudbackup.database.mongodb");
 */
class MongoDB extends BaseDatabase
{
    const DB_PATH = 'mongo';


    /*public function __construct()
    {
        $this->kernelCacheDir = $kernelCacheDir;

        $path   = $this->kernelCacheDir . '/db/mongo/';
        $file   = new Filesystem();
        $file->mkdir($path);
    }*/

    public function dump()
    {
        parent::before();

        $cmd    = sprintf('mongodump --db creditmanager --out %s',
                           $this->dataPath);

        exec($cmd);

        parent::after();
    }






}