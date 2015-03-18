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
     * @param string $filePath
     */
    public function upload($filePath)
    {
        $exception = null;
        foreach ($this->children as $child) {
            $this->logger->info(sprintf('[Dizda Backup] Uploading to %s', $child->getName()));

            try {
                $child->upload($filePath);
            } catch (\Exception $e) {
                //save the exception for later, there might be other children that are working
                $exception = $e;
            }
        }

        if ($exception) {
            throw $exception;
        }
    }
}
