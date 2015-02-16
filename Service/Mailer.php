<?php

namespace Dizda\CloudBackupBundle\Service;

/**
 * Class Mailer
 *
 * @author Jonathan Dizdarevic <dizda@dizda.fr>
 */
class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param \Swift_Mailer $mailer
     * @param array         $params
     */
    public function __construct(\Swift_Mailer $mailer, array $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;
    }

    /**
     * @param \Exception $exception
     */
    public function sendException(\Exception $exception)
    {
        // If there is no recipient, do not send email
        if (count($this->params['to']) === 0) {
            return;
        }

        $message = $this->mailer->createMessage()
            ->setFrom($this->params['from'])
            ->setTo($this->params['to'])
            ->setSubject('DizdaBackupBundle: Backup error')
            ->setBody(
                $exception->getMessage() . '( code: ' .
                $exception->getCode()    . '; file: ' .
                $exception->getFile()    . '; line: ' .
                $exception->getLine()    . ')'
            )
        ;

        $this->mailer->send($message);
    }
}
