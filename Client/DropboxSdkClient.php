<?php
namespace Dizda\CloudBackupBundle\Client;

use Symfony\Component\Console\Output\ConsoleOutput;
use \Dropbox as dbx;

/**
 * Class DropboxClient.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class DropboxSdkClient implements ClientInterface
{
    private $access_token;

    /**
     * @param array $params user
     */
    public function __construct($params)
    {
        $params             = $params['dropbox_sdk'];
        $this->access_token = $params['access_token'];
        $this->remotePath   = $params['remote_path'];
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $fileName = explode('/', $archive);
        
        $pathError = dbx\Path::findErrorNonRoot($this->remotePath);
        if ($pathError !== null) {
            fwrite(STDERR, "Invalid <dropbox-path>: $pathError\n");
            die;
        }

        $client = new dbx\Client($this->access_token, "CloudBackupBundle");
                
        $size = \filesize($archive);   

        $fp = fopen($archive, "rb");
        $metadata = $client->uploadFile($this->remotePath."/".end($fileName), dbx\WriteMode::add(), $fp, $size);
        fclose($fp);

        //print_r($metadata); // Printing response form Dropbox
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'DropboxSdk';
    }
}
