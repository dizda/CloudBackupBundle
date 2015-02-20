<?php
namespace Dizda\CloudBackupBundle\Client;

use Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class GoogleDriveClient
 *
 * @package Dizda\CloudBackupBundle\Client
 * @author  Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class GoogleDriveClient implements ClientInterface
{
    /**
     * @var ConsoleOutput output
     */
    protected $output;

    /**
     * @var \Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider clientProvider
     */
    protected $clientProvider;

    /**
     * @var string remotePath
     */
    protected $remotePath;

    /**
     * @param ClientProvider $clientProvider
     * @param string $tokenName
     * @param string $remotePath
     */
    public function __construct(ClientProvider $clientProvider, $tokenName, $remotePath)
    {
        $this->output = new ConsoleOutput();
        $this->clientProvider = $clientProvider;
        $this->tokenName = $tokenName;
        $this->remotePath = $remotePath;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $service = $this->getDriveService();
        $this->handleUpload($service, $archive);
        $this->output->writeln('<info>OK</info>');
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
     * @throws \Symfony\Component\Security\Core\Exception\LockedException
     */
    protected function getParentFolder(\Google_Service_Drive $service)
    {
        $parts = explode('/', ltrim($this->remotePath,'/'));
        $folderId=null;
        foreach ($parts as $name) {
            $q = 'mimeType="application/vnd.google-apps.folder" and title contains "' . $name . '"';
            if ($folderId) {
                $q.=sprintf(' and "%s" in parents', $folderId);
            }
            $folders = $service->files->listFiles(array(
                    'q'=> $q,
                ))->getItems();
            if (count($folders)==0) {
                //TODO create the missing folder.
                throw new \LogicException('Remote path does not exist.');
            } else {
                /** @var \Google_Service_Drive_DriveFile $folders[0] */
                $folderId=$folders[0]->id;
            }
        }

        if (!$folderId) {
            return null;
        }

        $parent = new \Google_Service_Drive_ParentReference();
        $parent->setId($folderId);

        return $parent;
    }

    /**
     *
     * @return \Google_Service_Drive
     */
    protected function getDriveService()
    {
        $client = $this->clientProvider->getClient($this->tokenName);
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
     * @param $message
     * @param bool $newLine
     */
    protected function output($message, $newLine=true)
    {
        if ($newLine) {
            $this->output->writeln($message);
        } else {
            $this->output->write($message);
        }

    }

    /**
     * @param $archive
     *
     * @return string
     */
    protected function getFileContents($archive)
    {
        $data = @file_get_contents($archive);

        if ($data === false) {
            throw new \Exception(sprintf('Could not read file: %s', $archive));
        }

        return $data;
    }

    /**
     * @param $service
     * @param $archive
     * @return mixed
     * @throws \Exception
     */
    private function handleUpload($service, $archive)
    {

        $file = $this->getDriveFile($archive);

        $mime = $this->getMimeType($archive);
        $file->setMimeType($mime);

        if ($this->remotePath !== '/') {
            $parent=$this->getParentFolder($service);

            if ($parent) {
                $file->setParents(array($parent));
            }
        }

        return $service->files->insert($file, array(
            'data' => $this->getFileContents($archive),
            'mimeType' => $mime,
            'uploadType' => 'media',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'GoogleDrive';
    }
}