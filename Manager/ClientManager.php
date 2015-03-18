<?php

namespace Dizda\CloudBackupBundle\Manager;

use Dizda\CloudBackupBundle\Client\ClientInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ClientChain.
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class ClientManager
{
    /**
     * @var ClientInterface[] children
     */
    protected $children;

    /**
     * @var \Psr\Log\LoggerInterface logger
     */
    protected $logger;

    /**
     * @param LoggerInterface   $logger
     * @param ClientInterface[] $clients
     */
    public function __construct(LoggerInterface $logger, array $clients = array())
    {
        $this->logger = $logger;
        $this->children = $clients;
    }

    /**
     * Add a client to the chain.
     *
     * @param ClientInterface $client
     */
    public function add(ClientInterface $client)
    {
        $this->children[] = $client;
    }

    /**
     * Upload to all active clients.
     *
     * @param array $files is an array with file paths
     */
    public function upload($files)
    {
        foreach ($this->children as $child) {
            $this->logger->info(sprintf('[Dizda Backup] Uploading to %s', $child->getName()));
            foreach ($files as $file) {
                $child->upload($file);
            }
        }
    }
}
