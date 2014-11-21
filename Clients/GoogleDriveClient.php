<?php
namespace Dizda\CloudBackupBundle\Clients;

use Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class GoogleDriveClient
 *
 * @package Dizda\CloudBackupBundle\Clients
 * @author  Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class GoogleDriveClient implements ClientInterface
{
    /**
     * @var ConsoleOutput output
     */
    private $output;

    /**
     * @var \Happyr\GoogleSiteAuthenticatorBundle\Service\ClientProvider clientProvider
     */
    private $clientProvider;

    /**
     * @var string remotePath
     */
    private $remotePath;

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
     * Do the actual upload
     *
     * @param $archive
     */
    public function upload($archive)
    {
        $this->output->write('- <comment>Uploading to Google Drive...</comment>');

        if (!class_exists('Google_Client')) {
            $this->output->write('- <error>You need to add google/apiclient to your composer.json.</error>');
            return;
        }

        $client = $this->clientProvider->getClient($this->tokenName);

        $service = new \Google_Service_Drive($client);
        $mime = $this->getMimeType($archive);
        $filename= basename($archive);

        $file = new \Google_Service_Drive_DriveFile();
        $file->setTitle($filename);
        $file->setMimeType($mime);

        if ($this->remotePath !== '/') {
            $parent=$this->getParentFolder($service);

            if ($parent) {
                $file->setParents(array($parent));
            }
        }

        $result = $service->files->insert($file, array(
            'data' => file_get_contents($archive),
            'mimeType' => $mime,
            'uploadType' => 'media',
        ));

        $this->output->writeln('<info>OK</info>');
    }

    /**
     * @param $file
     *
     * @return string
     */
    private function getMimeType($file)
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
    private function getParentFolder(\Google_Service_Drive $service)
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
}