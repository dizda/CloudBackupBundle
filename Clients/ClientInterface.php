<?php
namespace Dizda\CloudBackupBundle\Clients;

/**
 * Class ClientInterface
 *
 * @package Dizda\CloudBackupBundle\Clients
 * @author  Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface ClientInterface
{
    public function upload($archive);
}