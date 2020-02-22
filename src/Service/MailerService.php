<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService extends AbstractController
{
    private $data = [];
    private $emailData = ['from', 'to', 'subject', 'text'];
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function setData($data)
    {
        $this->data = $data;

        try {

            $this->validateData();

        } catch (\Exception $e) {

            echo $e->getMessage();

            die;
        }
    }

    public function sendEmail()
    {
        $email = $this->createEmail();
        $sentEmail = $this->mailer->send($email);
    }

    private function validateData()
    {
        foreach($this->emailData as $v) {
            if(!array_key_exists($v, $this->data)){
                throw new \Exception('This key "' . $v .'" is missed, You should provide it!');
            }
        }
    }

    private function createEmail()
    {
        $email = (new Email())
            ->from($this->data['from'])
            ->to($this->data['to'])
            ->subject($this->data['subject'])
            ->text($this->data['text'])
            ->html('<p>See Twig integration for better HTML integration!</p>');

        return $email;
    }
}