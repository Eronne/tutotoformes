<?php

namespace AppBundle;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class Utils
{


    /**
     * @var \Swift_Mailer $mailer
     */
    private $_mailer;
    private $_em;

    public function __construct(\Swift_Mailer $mailer, EntityManager $em)
    {
        $this->_em = $em;
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
    public function sendMail($subject, $addresses, $name = null, $to, $body, $body_type)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject($subject)->setFrom($addresses, $name)->setTo($to)->setBody($body, $body_type);
        $this->_mailer->send($message);
    }

    public function createEntityFromParameters($entity, Request $request)
    {
        switch ($entity) {
            case "AppBundle:Tutoriel":
                $params = $request->get('_tutoriel');
                if (!$params) {
                    throw new Exception('Le paramètre "tutoriel" n a pas été trouvé dans les paramètres de la requête');
                }
                $tutoriel = new Tutoriel();
                $tutoriel->setAuthor($this->_em->getRepository('AppBundle:Utilisateur')->find($params['author']))
                    ->setCreatedAt(new \DateTime('now'))
                    ->setDifficulty($params['difficulty'])
                    ->setDuration($params['duration'])
                    ->setSubtitle($params['subtitle'])
                    ->setIsDraft((key_exists('draft', $params)) ? 1 : 0)
                    ->setTitle($params['title']);
                return $tutoriel;
                break;
            case "AppBundle:TutorielPage":
                $params = $request->get('_tutorielpage');
                if (!$params) {
                    throw new Exception('Le paramètre "tutorielpage" n a pas été trouvé dans les paramètres de la requête');
                }
                $tutorielPage = new TutorielPage();
                $tutorielPage->setCreatedAt(new \DateTime('now'))
                    ->setTitle($params['title'])
                    ->setPageNumber($params['page_number'])
                    ->setContent($params['content']);
                return $tutorielPage;
                break;
            default:
                throw new \LogicException('Veuillez spécifier une entité valide');
                break;
        }
    }

    /**
     * @param $entity Tutoriel|TutorielPage
     * @param Request $request
     */
    public function updateEntityFromParameters(&$entity, Request $request)
    {
        if ($entity instanceof Tutoriel) {
            $params = $request->get('_tutoriel');
            if (!$params) {
                throw new Exception('Le paramètre "tutoriel" n a pas été trouvé dans les paramètres de la requête');
            }
            $entity->setAuthor($this->_em->getRepository('AppBundle:Utilisateur')->find($params['author']))
                ->setDifficulty($params['difficulty'])
                ->setDuration($params['duration'])
                ->setSubtitle($params['subtitle'])
                ->setTitle($params['title'])
                ->setIsDraft((key_exists('draft', $params)) ? 1 : 0)
                ->setEditedAt(new \DateTime('now'));
        } elseif ($entity instanceof TutorielPage) {
            $params = $request->get('_tutorielpage');
            if (!$params) {
                throw new Exception('Le paramètre "tutorielpage" n a pas été trouvé dans les paramètres de la requête');
            }
            $entity->setEditedAt(new \DateTime('now'))
                ->setContent($params['content'])
                ->setPageNumber($params['page_number'])
                ->setTitle($params['title']);
        } else {
            throw new LogicException("Le type de l'entité n'est pas pris en charge");
        }
    }

}