<?php
namespace Dizda\CloudBackupBundle\Clients;

/**
 * Class CloudAppClient
 *
 * @package Dizda\CloudBackupBundle\Clients
 * @author  Jonathan Dizdarevic <dizda@dizda.fr>
 */
interface ClientInterface
{
    public function upload($archive);
}