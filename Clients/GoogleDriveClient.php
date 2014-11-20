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
     * @var string tokenName
     */
    private $tokenName;

    /**
     * @param ClientProvider $clientProvider
     * @param string $tokenName
     */
    public function __construct(ClientProvider $clientProvider, $tokenName)
    {
        $this->output = new ConsoleOutput();
        $this->clientProvider = $clientProvider;
        $this->tokenName = $tokenName;
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

        $file = new \Google_Service_Drive_DriveFile();
        $result = $service->files->insert($file, array(
            'data' => file_get_contents($archive),
            'mimeType' => $mime,
            'uploadType' => 'media'
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
}