<?php

namespace App\Util;

use App\Constant\Constant;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailService
{
    private $from;
    private MailerInterface $mailer;

    public function __construct(string $from, MailerInterface $mailer)
    {
        $this->from   = $from;
        $this->mailer = $mailer;
    }

    /**
     * Permet d'envoyer un email
     *
     * @param string $to Adresse email du destinataire
     * @param string $subject Objet du message
     * @param string $text Contenu brut du message
     * @param string|null $html Contenu HTML du message
     *
     * @return array Array retournant l'état de l'envoi (true pour succès, false pour échec)
     * et un message d'information associé
     */
    public function sendEmail(string $to, string $subject, string $text, string $html = null)
    {
        $email = (new Email())
            ->from($this->from)
            ->to($to)
            ->subject($subject)
            ->text($text)
            ->html($html);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return [
                'success' => false,
                'message' => Constant::EMAIL_SEND_ERROR . ' : ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => Constant::EMAIL_SEND_ERROR
            ];
        }

        return [
            'success' => true,
            'message' => Constant::EMAIL_SEND_SUCCESS
        ];
    }
}
