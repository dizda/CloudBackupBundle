<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;
use JMS\DiExtraBundle\Annotation as DI;

use CloudApp\API as CloudApp;

/**
 * @DI\Service("dizda.cloudbackup.client.cloudapp");
 */
class CloudAppClient
{
    private $output;
    private $user;
    private $password;

    /**
     * @DI\InjectParams({
     *     "user"         = @DI\Inject("%dizda_cloud_backup.cloud_storages.cloudapp.user%"),
     *     "password"     = @DI\Inject("%dizda_cloud_backup.cloud_storages.cloudapp.password%")
     * })
     */
    public function __construct($user, $password)
    {
        $this->output     = new ConsoleOutput();
        $this->user       = $user;
        $this->password   = $password;
    }


    public function upload($archive)
    {
        $this->output->write('- <comment>Uploading to CloudApp...</comment>');

        $cloudapp = new CloudApp($this->user, $this->password);
        $cloudapp->addFile($archive);

        $this->output->writeln('<info>OK</info>');
    }

}