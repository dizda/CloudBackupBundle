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
     * TODO: Add a config prefix to archive (with default value : '')
     * TODO: Few compression mode
     */
    final public function before()
    {
        $localDate = date('Y_m_d-H_i_s');

        $this->basePath     = $this->kernelCacheDir . '/db/' . static::DB_PATH . '/';
        $this->dataPath     = $this->basePath . $localDate . '/';
        $this->archivePath  = $this->basePath . $localDate . '.tar';


        $this->fileSystem->mkdir($this->dataPath);
    }

    final public function after()
    {
        $archive = sprintf('tar -czf %s %s',
                            $this->archivePath,
                            $this->dataPath);

        exec($archive);

        $this->fileSystem->remove($this->dataPath);
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