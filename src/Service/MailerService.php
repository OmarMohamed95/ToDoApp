<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * MailerService
 */
class MailerService extends AbstractController
{
    /** @var array */
    private $data = [];

    /** @var array */
    private $emailData = ['from', 'to', 'subject', 'text'];

    /** @var MailerInterface */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Set data
     *
     * @param array $data
     *
     * @throws Exception
     *
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;

        try {
            $this->validateData();
        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * Send email action
     *
     * @return void
     */
    public function sendEmail(): void
    {
        $email = $this->createEmail();
        $sentEmail = $this->mailer->send($email);
    }

    /**
     * validate data
     *
     * @throws Exception
     *
     * @return void
     */
    private function validateData(): void
    {
        foreach ($this->emailData as $v) {
            if (!array_key_exists($v, $this->data)) {
                throw new \Exception(sprintf('This key %s is missed, You should provide it!', $v));
            }
        }
    }

    /**
     * Create email
     *
     * @return Email
     */
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
