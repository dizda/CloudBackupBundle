<?php

namespace Dizda\CloudBackupBundle\Chain;

use Dizda\CloudBackupBundle\Clients\ClientInterface;

/**
 * Class ClientChain
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class ClientChain
{
    /**
     * @var array
     */
    protected $clients;

    /**
     * @param array $clients
     */
    public function __construct(array $clients = array())
    {
        $this->clients = $clients;
    }

    /**
     * Add a client to the chain
     *
     * @param ClientInterface $client
     */
    public function add(ClientInterface $client)
    {
        $this->clients[] = $client;
    }

    /**
     * Upload to all active clients
     */
    public function upload()
    {
        foreach ($this->clients as $client) {
            $client->upload();
        }
    }
}