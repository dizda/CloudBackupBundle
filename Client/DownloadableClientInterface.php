<?php
namespace Dizda\CloudBackupBundle\Client;

interface DownloadableClientInterface
{
    /**
     * Download lastest backup file.
     *
     * @return \SplFileInfo
     */
    public function download();
}
