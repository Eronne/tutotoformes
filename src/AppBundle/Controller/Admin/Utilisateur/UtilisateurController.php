<?php

namespace AppBundle\Controller\Admin\Utilisateur;

use AppBundle\Entity\Role;
use AppBundle\Entity\Utilisateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UtilisateurController
 * @package AppBundle\Controller
 * @Route("/admin/user")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UtilisateurController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $name
     * @Route("/", name="admin_user_index")
     */
    public function indexAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:Utilisateur')->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @param Request $request
     * @Route("/delete/{id}", name="admin_user_delete")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Utilisateur $user){
        if($user == $this->getUser()) {
            $this->addFlash("notification error", 'Vous ne pouvez pas vous supprimer lorsque vous êtes connecté !');
            return $this->redirectToRoute('admin_user_index');
        }
        $em = $this->getDoctrine()->getManager();
        $up = $this->getDoctrine()->getRepository('AppBundle:UserProgression')->findOneBy(['user' => $user]);
        if($up) {
            $up->setUser(null)
                ->setTutoriel(null);
            $em->remove($up);
            $em->flush();

        }
        $em->remove($user);
        $em->flush();
        $this->addFlash('notification success', "L'utilisateur a bien été supprimé !");
        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @param Request $request
     * @param Utilisateur $user
     * @Route("/edit/{username}", name="admin_user_edit")
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Utilisateur $user){
        if($request->getMethod() == "GET"){
            /** @var Role[] $roles */
            $roles = $this->getDoctrine()->getRepository('AppBundle:Role')->findAll();
            return $this->render('admin/user/edit.html.twig', ['user' => $user, 'roles' => $roles]);
        } else {
            $newRolesId = $request->get('role');
            $roleRepository = $this->getDoctrine()->getRepository('AppBundle:Role');
            foreach ($user->getRolesObject() as $role) {
                $user->removeRole($role);
            }
            foreach ($newRolesId as $roleId) {
                $user->addRole($roleRepository->findOneBy(['id' => $roleId]));
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("notification success", "Les rôles ont correctement été mis à jour");
            return $this->redirectToRoute('admin_user_index');
        }
    }


}
