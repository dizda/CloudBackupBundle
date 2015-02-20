<?php
namespace Dizda\CloudBackupBundle\Client;

/**
 * Class ClientInterface
 *
 * @package Dizda\CloudBackupBundle\Client
 * @author  Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface ClientInterface
{
    public function upload($archive);
}