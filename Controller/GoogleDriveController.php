<?php

namespace Dizda\CloudBackupBundle\Controller;

use Rawls\BaseBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Tobias Nyholm
 */
class GoogleDriveController extends BaseController
{

    public function accessTokenAction(Request $request)
    {
        /** @var \Google_Client $client */
        $client = $this->get('dizda.cloudbackup.client.google_drive')->getClient();

        if ($request->query->has('code')) {
            try {
                $client->authenticate($request->query->get('code'));

                $body=$client->getAccessToken();
            } catch (\Google_Auth_Exception $e) {
                $body = $e->getMessage();
            }
        } else {
            $body= sprintf('<a href="%s">Authenticate</a>', $client->createAuthUrl());
        }

        return new Response($body);
    }

} 