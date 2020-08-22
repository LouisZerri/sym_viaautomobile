<?php


namespace App\notification;


use App\Entity\User;
use Twig\Environment;

class EmailNotification
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;


    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(User $user)
    {
        $message = (new \Swift_Message('Confirmation de votre compte'))
            ->setFrom('noreply@viaautomobile.fr')
            ->setTo($user->getEmail())
            ->setReplyTo($user->getEmail())
            ->setBody($this->renderer->render('emails/contact.html.twig', [
                'user' => $user
            ]), 'text/html');

        $this->mailer->send($message);
    }

    public function sendEmailForResetPassword(User $user)
    {
        $message = (new \Swift_Message('Réinitialisation de votre mot de passe'))
            ->setFrom('noreply@viaautomobile.fr')
            ->setTo($user->getEmail())
            ->setReplyTo($user->getEmail())
            ->setBody($this->renderer->render('emails/reset.html.twig', [
                'user' => $user
            ]), 'text/html');

        $this->mailer->send($message);
    }




}