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
     * @var ClientInterface[] children
     */
    protected $children;

    /**
     * @param array $clients
     */
    public function __construct(array $clients = array())
    {
        $this->children = $clients;
    }

    /**
     * Add a client to the chain
     *
     * @param ClientInterface $client
     */
    public function add(ClientInterface $client)
    {
        $this->children[] = $client;
    }

    /**
     * Upload to all active clients
     *
     * @param string $filePath
     */
    public function upload($filePath)
    {
        foreach ($this->children as $child) {
            $child->upload($filePath);
        }
    }

    public function getName()
    {
        $names = array();
        foreach ($this->children as $child) {
            $names[] = $child->getName();
        }

        return sprintf('ClientChain of %d (%s)', count($names), implode(', ', $names));
    }
}