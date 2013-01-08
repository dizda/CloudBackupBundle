<?php
namespace Dizda\CloudBackupBundle\Databases;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Filesystem\Filesystem;


/**
 * @DI\Service("dizda.cloudbackup.database.mongodb");
 */
class MongoDB
{
    private $kernelCacheDir;

    /**
     * @DI\InjectParams({
     *     "kernelCacheDir" = @DI\Inject("%kernel.cache_dir%")
     * })
     */
    public function __construct($kernelCacheDir)
    {
        $this->kernelCacheDir = $kernelCacheDir;
    }

    public function dump()
    {
        $path   = $this->kernelCacheDir . '/db/mongo/';
        $file   = new Filesystem();
        $file->mkdir($path);

        $cmd    = "mongodump --db creditmanager --out $path";
        exec($cmd);

        var_dump($path);

    }



}