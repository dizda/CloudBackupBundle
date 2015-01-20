<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;

use CloudApp\API as CloudApp;

/**
 * Class CloudAppClient
 *
 * @package Dizda\CloudBackupBundle\Clients
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
class CloudAppClient implements ClientInterface
{
    private $output;
    private $user;
    private $password;

    /**
     * @param array $params
     */
    public function __construct($params)
    {
        $this->output     = new ConsoleOutput();
        $this->user       = $params['user'];
        $this->password   = $params['password'];
    }


    public function upload($archive)
    {
        $this->output->write('- <comment>Uploading to CloudApp...</comment>');

        $cloudapp = new CloudApp($this->user, $this->password);
        $cloudapp->addFile($archive);

        $this->output->writeln('<info>OK</info>');
    }
}
