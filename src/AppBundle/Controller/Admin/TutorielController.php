<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use AppBundle\Entity\UserProgression;
use AppBundle\Entity\Utilisateur;
use Doctrine\Common\Collections\Criteria;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TutorielController
 * @package AppBundle\Controller\Admin
 */
class TutorielController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/tutoriel/", name="admin_tutoriels_index")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $tutoriels = $this->getDoctrine()->getRepository('AppBundle:Tutoriel')->findAll('DESC', true);
        return $this->render('tutoriel/index.html.twig', ['tutoriels' => $tutoriels]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Exception
     * @Route("/admin/tutoriel/add", name="admin_tutoriel_add")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_WRITER')")
     */
    public function addAction(Request $request)
    {
        if ($request->getMethod() == "GET") {
            $users = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findAll();
            return $this->render('tutoriel/add.html.twig', ['users' => $users]);
        } else {

            $params = $request->get('_tutoriel');

            /** @var UploadedFile $file */

            $file = $request->files->get('_tutoriel')['thumbnailImage'];

            if (!$params) {
                throw new Exception('Le paramètre "tutoriel" n a pas été trouvé dans les paramètres de la requête');
            }

            $tutoriel = new Tutoriel();
            $tutoriel->setAuthor($this->getDoctrine()->getRepository('AppBundle:Utilisateur')->find($params['author']))
                ->setCreatedAt(new \DateTime('now'))
                ->setDifficulty($params['difficulty'])
                ->setDuration($params['duration'])
                ->setSubtitle($params['subtitle'])
                ->setIsDraft((key_exists('draft', $params)) ? 1 : 0)
                ->setThumbnailLink($params['thumbnail'])
                ->setDescription($params['description'])
                ->setTitle($params['title']);

            if ($file != null) {
                if ($this->get('app.utils')->isValidFile($file, "image/.*")) {
                    $tutoriel->setThumbnailFile($file);
                } else {
                    $this->addFlash('notification error', "Veuillez uploader une image valide !");
                    return $this->redirectToRoute('admin_tutoriel_add');
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($tutoriel);
            $em->flush();
            $this->addFlash('notification success', "Le tutoriel a bien été ajouté !");

            return $this->redirectToRoute('admin_tutoriels_index');
        }
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws Exception
     * @Route("/admin/tutoriel/edit/{id}", name="admin_tutoriel_edit")
     * @Security("has_role('ROLE_ADMIN') or (has_role('ROLE_WRITER') and tutoriel.getAuthor() == user)")
     */
    public function editAction(Request $request, Tutoriel $tutoriel)
    {
        if (!$tutoriel) {
            return $this->createNotFoundException();
        }

        $users = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findByRoles(['ROLE_ADMIN', 'ROLE_WRITER']);
        if ($request->getMethod() === "GET") {
            return $this->render('tutoriel/edit.html.twig', ['tutoriel' => $tutoriel, 'users' => $users]);
        } else {
            $em = $this->getDoctrine()->getManager();

            $params = $request->get('_tutoriel');
            $file = $request->files->get('_tutoriel')['thumbnailImage'];

            if (!$params) {
                throw new Exception('Le paramètre "tutoriel" n a pas été trouvé dans les paramètres de la requête');
            }

            $tutoriel->setAuthor($em->getRepository('AppBundle:Utilisateur')->find($params['author']))
                ->setDifficulty($params['difficulty'])
                ->setDuration($params['duration'])
                ->setSubtitle($params['subtitle'])
                ->setTitle($params['title'])
                ->setIsDraft((key_exists('draft', $params)) ? 1 : 0)
                ->setThumbnailLink($params['thumbnail'])
                ->setDescription($params['description'])
                ->setEditedAt(new \DateTime('now'));
            if ($file != null) {
                if ($this->get('app.utils')->isValidFile($file, "image/.*")) {
                    $tutoriel->setThumbnailFile($file);
                } else {
                    $this->addFlash('notification error', "Veuillez uploader une image valide !");
                    return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);
                }
            }

            $em->flush();
            $this->addFlash('notification success', "Le tutoriel a bien été modifié");
            return $this->redirectToRoute('admin_tutoriel_edit', ['id' => $tutoriel->getId()]);
        }
    }

    /**
     * @param Request $request
     * @param Tutoriel $tutoriel
     * @Route("/admin/tutoriel/remove/{id}", name="admin_tutoriel_remove")
     * @Security("has_role('ROLE_ADMIN')")
     * @return \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeAction(Request $request, Tutoriel $tutoriel)
    {
        if (!$tutoriel) {
            return $this->createNotFoundException();
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($tutoriel);
        $em->flush();
        $this->addFlash('notification success', "Le tutoriel a bien été supprimé");
        return $this->redirectToRoute('admin_tutoriels_index');
    }






}
