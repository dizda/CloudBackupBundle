<?php
namespace Dizda\CloudBackupBundle\Client;

/**
 * Class ClientInterface.
 *
 * @author  Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface ClientInterface
{
    /**
     * Upload a file to the cloud client.
     *
     * @param string $archive
     *
     * @return bool
     */
    public function upload($archive);

    /**
     * The name of the client.
     *
     * @return string
     */
    public function getName();
}
