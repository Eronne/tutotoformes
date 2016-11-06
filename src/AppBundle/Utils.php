<?php

namespace AppBundle;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use AppBundle\Entity\Utilisateur;
use AppBundle\Entity\UtilisateurAchievementAssociation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class Utils
{


    /**
     * @var \Swift_Mailer $mailer
     */
    private $_mailer;
    private $_em;

    private $_container;

    public function __construct(\Swift_Mailer $mailer, EntityManager $em, ContainerInterface $container)
    {
        $this->_em = $em;
        $this->_mailer = $mailer;
        $this->_container = $container;
    }

    public function unlockAchievement($achievementInternalName, Utilisateur $user)
    {
        $achievement = $this->_em->getRepository('AppBundle:Achievement')->findOneBy(['internalName' => $achievementInternalName]);
        if (!$achievement) return;
        if (!$this->_em->getRepository('AppBundle:UtilisateurAchievementAssociation')->haveUserUnlocked($user, $achievement)) {
            $userAchievement = new UtilisateurAchievementAssociation();
            $userAchievement->setAchievement($achievement)
                ->setUtilisateur($user)
                ->setUnlockedAt(new \DateTime('now'));
            $this->_em->persist($userAchievement);
            $this->_em->flush();
            $this->_container->get('session')->getFlashBag()->add('alert success', "Félicitations ! Tu as obtenu le succès '" . $achievement->getTitle() . "'");
        }
    }

    public function isValidFile(UploadedFile $file, $acceptedMimeType)
    {
        return ($file != null && $file->isValid() && mb_ereg_match($acceptedMimeType, $file->getMimeType()));
    }

    /**
     * @param $subject string Le sujet du mail
     * @param $addresses string|string[] Adresse de l'expéditeur
     * @param $name string Alias pour l'expéditeur
     * @param $to string[] Adresse(s) à qui envoyer le mail
     * @param $body string Le corps du mail
     * @param $body_type string Le type MIME du corps
     */
    public function sendMail($subject, $addresses = ['no-reply@tutotoformes.fr'], $name = 'Tutotoformes', $to, $body, $body_type)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject)->setFrom($addresses, $name)->setTo($to)->setBody($body, $body_type);
        $this->_mailer->send($message);
    }


    public function str_random($length = 60)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function setNullIfEmptyString($s)
    {
        if ($s === '') {
            return null;
        }
        return $s;
    }

}