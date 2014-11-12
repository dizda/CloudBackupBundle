<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class GoogleDriveClient
 *
 * @package Dizda\CloudBackupBundle\Clients
 * @author  Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class GoogleDriveClient implements ClientInterface
{
    const APPLICATION_NAME = 'DizdaCloudBackup';
    const REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';

    /**
     * @var ConsoleOutput output
     */
    private $output;

    /**
     * @var string apiSecret
     */
    private $apiSecret;

    /**
     * @var string apiClientId
     */
    private $apiClientId;

    /**
     * @param string $apiClientId
     * @param string $apiSecret
     */
    public function __construct($apiClientId, $apiSecret)
    {
        $this->output = new ConsoleOutput();
        $this->apiClientId = $apiClientId;
        $this->apiSecret = $apiSecret;
    }

    public function upload($archive)
    {
        $this->output->write('- <comment>Uploading to Google Drive...</comment>');

        if (!class_exists('Google_Client')) {
            $this->output->write('- <error>You need to add google/apiclient to your composer.json.</error>');
            return;
        }

        $client = $this->getClient();

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

    /**
     *
     * @return \Google_Client
     */
    private function getClient()
    {
        $client = new \Google_Client();
        $client->setApplicationName(self::APPLICATION_NAME);
        $client->setClientId($this->apiClientId);
        $client->setClientSecret($this->apiSecret);
        $client->setRedirectUri(self::REDIRECT_URI);
        $client->setScopes(array('https://www.googleapis.com/auth/drive'));

        $authUrl = $client->createAuthUrl();

        //Request authorization
        print "Please visit:\n$authUrl\n\n";
        print "Please enter the auth code:\n";
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for access token
        $accessToken = $client->authenticate($authCode);
        $client->setAccessToken($accessToken);

        return $client;
    }
}