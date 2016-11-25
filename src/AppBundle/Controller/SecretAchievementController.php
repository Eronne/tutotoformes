<?php

namespace AppBundle\Controller;

use AppBundle\AchievementsName;
use AppBundle\Entity\Utilisateur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecretAchievementController extends Controller
{
    /**
     * @Route("/secret/mmi", name="unlock_secret_mmi_achievement")
     * @Security("has_role('ROLE_USER')")
     */
    public function secretAchievementAction(Request $request) {
        /** @var Utilisateur|string $user */
        $user = $this->getUser();
        if(!$user || $user == 'anon.') return $this->redirectToRoute('homepage');
        $this->get('app.utils')->unlockAchievement(AchievementsName::SECRET_MMI_ACHIEVEMENT, $user);
        return $this->redirectToRoute('my_achievements');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/secret/rabah", name="unlock_secret_rabah_achievement")
     * @Security("has_role('ROLE_USER')")
     */
    public function secretRabahAchievementAction(Request $request) {
        $user = $this->getUser();
        if(!$user || $user == 'anon.') return $this->redirectToRoute('homepage');
        $this->get('app.utils')->unlockAchievement(AchievementsName::SECRET_RABAH_ACHIEVEMENT, $user);
        return $this->redirectToRoute('my_achievements');
    }
}
