<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Achievement;
use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\Utilisateur;
use AppBundle\Repository\AchievementRepository;
use AppBundle\Repository\UtilisateurAchievementAssociationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends Controller
{
    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/profile/me", name="my_profile")
     * @Security("has_role('ROLE_USER')")
     */
    public function showMyProfileAction(Request $request)
    {
        /** @var Utilisateur $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $tutorielRepo = $this->getDoctrine()->getRepository('AppBundle:Tutoriel');
        $followingTutoriels = $tutorielRepo->getTutorielsStartedBy($user, false);

        $finishedTutoriels = $tutorielRepo->getFinishedTutorialsBy($user);
        $otherTutoriels = $tutorielRepo->findAll();
        $userRepo = $this->getDoctrine()->getRepository('AppBundle:UserProgression');



        $afterPagesCompleted = [];
        foreach($followingTutoriels as $followingTutoriel) {
            $afterPagesCompleted[] = $userRepo->getPageAfterLastCompleted($user, $followingTutoriel);
        }

        $pageRepo = $this->getDoctrine()->getRepository('AppBundle:TutorielPage');

        return $this->render('profile/me.html.twig', ['user' => $user, 'following_tutoriels' => $followingTutoriels, 'finished_tutoriels' => $finishedTutoriels, 'other_tutoriels' => $otherTutoriels, 'repo' => $pageRepo]);
    }

    /**
     * @param Request $request
     * @param Utilisateur $utilisateur
     * @Route("/profile/{username}", name="show_profile")
     */
    public function showProfileAction(Request $request, Utilisateur $utilisateur){
        if(!$utilisateur) throw new NotFoundHttpException("L'utilisateur n'a pas été trouvé");
        if($this->getUser() instanceof Utilisateur && $utilisateur->getUsername() == $this->getUser()->getUsername()) return $this->redirectToRoute('my_profile');

        return $this->render('profile/show.html.twig', ['user' => $utilisateur]);
    }

    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/profile/me/achievements", name="my_achievements")
     * @Security("has_role('ROLE_USER')")
     */
    public function showAchievementsAction(Request $request) {
        /** @var Utilisateur $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        /** @var UtilisateurAchievementAssociationRepository $userAchievementsRepo */
        $userAchievementsRepo = $this->getDoctrine()->getRepository('AppBundle:UtilisateurAchievementAssociation');

        /** @var Achievement[] $achievements */
        $achievements = $this->getDoctrine()->getRepository('AppBundle:Achievement')->findAll();

        $finishedTutorielsNb = count($this->getDoctrine()->getRepository('AppBundle:Tutoriel')->getFinishedTutorialsBy($user));


        return $this->render('profile/achievements.html.twig', ['user' => $user, 'achievements' => $achievements, 'user_achievements_repo' => $userAchievementsRepo, 'nb_finished_tutorials' => $finishedTutorielsNb]);
    }
}
