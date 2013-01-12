<?php
namespace Dizda\CloudBackupBundle\Databases;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Filesystem\Filesystem;



abstract class BaseDatabase
{
    const DB_PATH = '';

    protected $kernelCacheDir;
    protected $fileSystem;
    protected $basePath;
    protected $dataPath;
    protected $archivePath;



    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }


    /**
     * Preparation of directory
     *
     * $this->basePath      /Users/high/Sites/dizdabundles/app/cache/dev/db/
     * $this->dataPath      /Users/high/Sites/dizdabundles/app/cache/dev/db/mongo/
     * $this->archivePath   /Users/high/Sites/dizdabundles/app/cache/dev/db/bambou_2013_01_12-01_36_33.tar
     *
     * TODO: Add a config prefix to archive (with default value : '')
     * TODO: Many compression mode
     */
    final public function prepare()
    {
        $this->basePath     = $this->kernelCacheDir . '/db/';
        $this->dataPath     = $this->basePath . static::DB_PATH . '/';

        $this->fileSystem->mkdir($this->dataPath);
    }


    /**
     * Compress with format name like : hostname_2013_01_12-00_06_40.tar
     */
    final public function compression()
    {
        $localDate          = date('Y_m_d-H_i_s');
        $this->archivePath  = $this->basePath . gethostname() . '_' . $localDate . '.tar';


        $archive = sprintf('tar -czf %s -C %s . 2>/dev/null',
                            $this->archivePath,
                            $this->basePath);

        exec($archive);
    }


    /**
     * Remove all dirs with files
     *
     */
    final public function cleanUp()
    {
        $this->fileSystem->remove($this->basePath);
    }


    /**
     * Migration procedure for each databases type
     *
     * @return mixed
     */
    abstract public function dump();


    public function getArchivePath()
    {
        return $this->archivePath;
    }


    /**
     * @DI\InjectParams({
     *     "kernelCacheDir" = @DI\Inject("%kernel.cache_dir%")
     * })
     */
    public function setKernelCacheDir($kernelCacheDir)
    {
        $this->kernelCacheDir = $kernelCacheDir;
    }

}