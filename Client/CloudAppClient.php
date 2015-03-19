<?php

namespace Dizda\CloudBackupBundle\Client;

use Symfony\Component\Console\Output\ConsoleOutput;
use CloudApp\API as CloudApp;

/**
 * Class CloudAppClient.
 *
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class CloudAppClient implements ClientInterface
{
    private $user;
    private $password;

    /**
     * @param array $params
     */
    public function __construct($params)
    {
        $this->user       = $params['user'];
        $this->password   = $params['password'];
    }

    /**
     * {@inheritdoc}
     */
    public function upload($archive)
    {
        $cloudapp = new CloudApp($this->user, $this->password);
        $cloudapp->addFile($archive);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'CloudApp';
    }
}
