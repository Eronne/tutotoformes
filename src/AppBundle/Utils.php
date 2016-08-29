<?php

namespace AppBundle;

class Utils {


    /**
     * @var \Swift_Mailer $mailer
     */
    private $_mailer;

    public function __construct(\Swift_Mailer $mailer)
    {

        $this->_mailer = $mailer;
    }

    /**
     * @param $subject string Le sujet du mail
     * @param $addresses string|string[] Adresse de l'expéditeur
     * @param $name string Alias pour l'expéditeur
     * @param $to string Adresse à qui envoyer le mail
     * @param $body string Le corps du mail
     * @param $body_type string Le type MIME du corps
     */
    public function sendMail($subject, $addresses, $name = null, $to, $body, $body_type){
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject)->setFrom($addresses, $name)->setTo($to)->setBody($body, $body_type);
        $this->_mailer->send($message);
    }

}