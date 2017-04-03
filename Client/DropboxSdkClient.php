<?php
namespace Dizda\CloudBackupBundle\Client;

use Dizda\CloudBackupBundle\Exception\InvalidConfigurationException;
use Dizda\CloudBackupBundle\Exception\RestoringNotAvailableException;
use Dizda\CloudBackupBundle\Exception\UploadException;
use \Dropbox as Dropbox;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DropboxSdkClient.
 *
 * @author David Fuertes
 */
class DropboxSdkClient implements ClientInterface, DownloadableClientInterface
{
    /**
     * @var string
     */
    private $access_token;

    /**
     * @var string
     */
    private $restoreFolder;

    /**
     * @var string
     */
    private $remoteMonthPath;

    /**
     * @var Filesystem
     */
    private $localFilesystem;

    /**
     * @var int
     */
    private $remote_files_count;

    /**
     * @var int
     */
    private $remote_month_count;

    /**
     *
     * @param array $params
     * @param $restoreFolder
     * @param Filesystem $localFilesystem
     */
    public function __construct($params, $restoreFolder = null, Filesystem $localFilesystem = null)
    {
        $this->restoreFolder = $restoreFolder;
        $this->localFilesystem = $localFilesystem;
        $params             = $params['dropbox_sdk'];
        $this->access_token = $params['access_token'];
        $this->remotePath   = $params['remote_path'];
        $this->remoteMonthPath   = $params['remote_month_path'];
        $this->file_prefix   = $params['file_prefix'];
        $this->remote_files_count   = $params['remote_files_count'];
        $this->remote_month_count   = $params['remote_month_count'];
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $fileName = explode('/', $archive);
        //change the path and limit if today is the first day of the month
        $files_path= (date('j') === '1') ? $this->remoteMonthPath:$this->remotePath;
        $limit_file= (date('j') === '1') ? $this->remote_month_count:$this->remote_files_count;
        $pathError = Dropbox\Path::findErrorNonRoot($files_path);

        if ($pathError !== null) {
            throw new UploadException(sprintf('Invalid path "%s".', $archive));
        }

        $client = new Dropbox\Client($this->access_token, 'CloudBackupBundle');
        $size = filesize($archive);

        $fp = fopen($archive, 'rb');
        $client->uploadFile($files_path . '/' . end($fileName), Dropbox\WriteMode::add(), $fp, $size);
        //get files list and remove if limit is matt
        $list_files = array_reverse($client->searchFileNames($files_path, $this->file_prefix));
        $i = 1;
        foreach ($list_files as $file) {
            if ($i > $limit_file) $client->delete($file['path']);
            $i++;
        }
        fclose($fp);
    }

    public function download()
    {
        if (!$this->restoreFolder) {
            throw InvalidConfigurationException::create('$restoreFolder');
        }
        $pathError = Dropbox\Path::findErrorNonRoot($this->remotePath);
        if ($pathError !== null) {
            throw new UploadException(sprintf('Invalid path "%s".', $this->remotePath));
        }
        $client = new Dropbox\Client($this->access_token, 'CloudBackupBundle');
        $entry = $client->getMetadataWithChildren($this->remotePath);
        if (!$entry['is_dir']) {
            throw RestoringNotAvailableException::create();
        }

        // Fetch the latest file
        $file = end($entry['contents']);
        $fileName = substr($file['path'], 1+strrpos($file['path'], '/'));
        $stream = fopen('php://temp', 'r+');
        $client->getFile($file['path'], $stream);
        fseek($stream, 0);
        $content = stream_get_contents($stream);

        $splFile = new \SplFileInfo($this->restoreFolder . $fileName);

        $this->localFilesystem->dumpFile($splFile->getPathname(), $content);

        return $splFile;
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'DropboxSdk';
    }
}
