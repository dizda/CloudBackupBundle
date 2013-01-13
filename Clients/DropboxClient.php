<?php
namespace Dizda\CloudBackupBundle\Clients;

use Symfony\Component\Console\Output\ConsoleOutput;
use JMS\DiExtraBundle\Annotation as DI;

use Dizda\CloudBackupBundle\Clients\DropboxUploader;

/**
 * @DI\Service("dizda.cloudbackup.client.dropbox");
 */
class DropboxClient
{
    private $output;
    private $user;
    private $password;
    private $remotePath;

    /**
     * @DI\InjectParams({
     *     "user"         = @DI\Inject("%dizda_cloud_backup.cloud_storages.dropbox.user%"),
     *     "password"     = @DI\Inject("%dizda_cloud_backup.cloud_storages.dropbox.password%"),
     *     "remotePath"   = @DI\Inject("%dizda_cloud_backup.cloud_storages.dropbox.remote_path%")
     * })
     */
    public function __construct($user, $password, $remotePath)
    {
        $this->output     = new ConsoleOutput();
        $this->user       = $user;
        $this->password   = $password;
        $this->remotePath = $remotePath;
    }

    public function upload($archive)
    {
        $this->output->writeln('- <comment>Uploading to Dropbox...</comment>');

        $dropbox = new DropboxUploader($this->user, $this->password);
        $dropbox->upload($archive, $this->remotePath);

        $this->output->writeln('- <info>Upload done</info>');
    }

}