<?php

namespace Dizda\CloudBackupBundle\Client;

/**
 * Class ClientChain
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class ClientChain implements ClientInterface
{
    /**
     * @var ClientInterface[] clients
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
     *
     * @param string $filePath
     */
    public function upload($filePath)
    {
        foreach ($this->clients as $client) {
            $client->upload($filePath);
        }
    }

    public function getName()
    {
        $names = array();
        foreach ($this->clients as $client) {
            $names[] = $client->getName();
        }

        return sprintf('ClientChain of %d (%s)', count($names), implode(', ', $names));
    }
}