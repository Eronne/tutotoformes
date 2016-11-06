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

class ProfileController extends Controller
{
    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/profile/me", name="my_profile")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Request $request)
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

        return $this->render('profile/achievements.html.twig', ['user' => $user, 'achievements' => $achievements, 'user_achievements_repo' => $userAchievementsRepo]);
    }
}
