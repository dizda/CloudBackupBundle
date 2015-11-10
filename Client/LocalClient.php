<?php
namespace Dizda\CloudBackupBundle\Client;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class LocalClient.
 *
 * @author  David Fuertes 
 */
class LocalClient implements ClientInterface
{
    private $path;

    /**
     * @param array $params user
     */
    public function __construct($params)
    {
        $params     = $params['local'];
        $this->path = $params['path'];
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $fileName = explode('/', $archive);
        $status   = copy($archive, $this->path."/".end($fileName));
        if(!$status){
            throw new \Exception('There was a problem moving backup file');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Local';
    }
}
