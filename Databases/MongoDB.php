<?php
namespace Dizda\CloudBackupBundle\Databases;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("dizda.cloudbackup.database.mongodb");
 */
class MongoDB extends BaseDatabase
{
    const DB_PATH = 'mongo';




    public function dump()
    {
        parent::prepare();

        $cmd    = sprintf('mongodump --db creditmanager --out %s',
                           $this->dataPath);

        exec($cmd);

    }






}