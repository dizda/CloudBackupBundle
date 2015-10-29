<?php

namespace Dizda\CloudBackupBundle\Client;

use Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider;

/**
 * Class GoogleDriveClient.
 *
 * @author  Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class GoogleDriveClient implements ClientInterface
{
    const CHUNK_SIZE_BYTES = 20971520; //20 * 1024 * 1024

    /**
     * @var \Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider clientProvider
     */
    protected $clientProvider;

    /**
     * @var string remotePath
     */
    protected $remotePath;

    /**
     * @var int
     */
    protected $timeout;

    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * @param ClientProvider $clientProvider
     * @param string         $tokenName
     * @param string         $remotePath
     * @param int            $timeout
     */
    public function __construct(ClientProvider $clientProvider, $tokenName, $remotePath, $timeout)
    {
        $this->clientProvider = $clientProvider;
        $this->tokenName = $tokenName;
        $this->remotePath = $remotePath;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $service = $this->getDriveService();
        $this->handleUpload($service, $archive);
    }

    /**
     * @param $file
     *
     * @return string
     */
    protected function getMimeType($file)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($info, $file);
        finfo_close($info);

        return $mime;
    }

    /**
     * @param \Google_Service_Drive $service
     *
     * @return \Google_Service_Drive_ParentReference|null
     *
     * @throws \Symfony\Component\Security\Core\Exception\LockedException
     */
    protected function getParentFolder(\Google_Service_Drive $service)
    {
        $parts = explode('/', trim($this->remotePath, '/'));
        $folderId = null;
        foreach ($parts as $name) {
            $q = 'mimeType="application/vnd.google-apps.folder" and title contains "'.$name.'"';
            if ($folderId) {
                $q .= sprintf(' and "%s" in parents', $folderId);
            }
            $folders = $service->files->listFiles(array(
                    'q' => $q,
                ))->getItems();
            if (count($folders) == 0) {
                //TODO create the missing folder.
                throw new \LogicException('Remote path does not exist.');
            } else {
                /* @var \Google_Service_Drive_DriveFile $folders[0] */
                $folderId = $folders[0]->id;
            }
        }

        if (!$folderId) {
            return;
        }

        $parent = new \Google_Service_Drive_ParentReference();
        $parent->setId($folderId);

        return $parent;
    }

    /**
     * @return \Google_Service_Drive
     */
    protected function getDriveService()
    {
        $client = $this->getClient();

        // Make sure CURL do not timeout on you when you upload a large file
        $client->setClassConfig('Google_IO_Curl', 'options',
            array(
                CURLOPT_TIMEOUT => $this->timeout,
            )
        );

        $service = new \Google_Service_Drive($client);

        return $service;
    }

    /**
     * @param $archive
     *
     * @return \Google_Service_Drive_DriveFile
     */
    protected function getDriveFile($archive)
    {
        $filename = basename($archive);

        $file = new \Google_Service_Drive_DriveFile();
        $file->setTitle($filename);

        return $file;
    }

    /**
     * @param \Google_Service_Drive $service
     * @param string                $archive
     *
     * @return \Google_Service_Drive_DriveFile
     *
     * @throws \Exception
     */
    private function handleUpload(\Google_Service_Drive $service, $archive)
    {
        $file = $this->getDriveFile($archive);

        $mime = $this->getMimeType($archive);
        $file->setMimeType($mime);

        if ($this->remotePath !== '/') {
            $parent = $this->getParentFolder($service);

            if ($parent) {
                $file->setParents(array($parent));
            }
        }

        $client = $this->getClient();
        // Call the API with the media upload, defer so it doesn't immediately return.
        $client->setDefer(true);
        $request = $service->files->insert($file);

        // Create a media file upload to represent our upload process.
        $media = $this->getMediaUploadFile($archive, $client, $request, $mime);

        return $this->uploadFileInChunks($archive, $media);
    }

    /**
     * Get a chunk of data from a file resource
     *
     * @param resource $handle
     * @param integer $chunkSize
     *
     * @return string
     */
    protected function readChunk($handle, $chunkSize)
    {
        $byteCount = 0;
        $giantChunk = '';
        while (!feof($handle)) {
            /*
             * fread will never return more than 8192 bytes if the stream is read
             * buffered and it does not represent a plain file
             */
            $chunk = fread($handle, 8192);
            $byteCount += strlen($chunk);
            $giantChunk .= $chunk;
            if ($byteCount >= $chunkSize) {
                return $giantChunk;
            }
        }

        return $giantChunk;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'GoogleDrive';
    }

    /**
     * @return \Google_Client
     */
    protected function getClient()
    {
        if ($this->client === null) {
            $this->client = $this->clientProvider->getClient($this->tokenName);
        }

        return $this->client;
    }

    /**
     * @param $archive
     * @param $media
     *
     * @return \Google_Service_Drive_DriveFile
     */
    protected function uploadFileInChunks($archive, $media)
    {
        $status = false;
        $handle = fopen($archive, 'rb');
        while (!$status && !feof($handle)) {
            $chunk = $this->readChunk($handle, self::CHUNK_SIZE_BYTES);
            $status = $media->nextChunk($chunk);
        }
        fclose($handle);

        return $status;
    }

    /**
     * @param $archive
     * @param $client
     * @param $request
     * @param $mime
     *
     * @return \Google_Http_MediaFileUpload
     */
    protected function getMediaUploadFile($archive, $client, $request, $mime)
    {
        $media = new \Google_Http_MediaFileUpload($client, $request, $mime, null, true, self::CHUNK_SIZE_BYTES);
        $media->setFileSize(filesize($archive));

        return $media;
    }
}
