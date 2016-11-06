<?php
/**
 * Created by IntelliJ IDEA.
 * User: Omar
 * Date: 06/11/2016
 * Time: 04:51
 */

namespace AppBundle\Events;


use AppBundle\Repository\TutorielRepository;

class TutorielListener
{

    public function onFinishedTutoriel(TutorielFinishedEvent $event) {
        /** @var TutorielRepository $tutorielRepo */
        $tutorielRepo = $event->getContainer()->get('doctrine')->getRepository('AppBundle:Tutoriel');
        $event->getProgression()->setFinishedAt(new \DateTime('now'));
        $event->getContainer()->get('doctrine')->getManager()->flush();
        $completedTutoriels = count($tutorielRepo->getFinishedTutorialsBy($event->getProgression()->getUser()));
        $event->getContainer()->get('session')->getFlashBag()->add('notification success', "Félicitations, vous avez fini le tutoriel '" .
            $event->getTutoriel()->getTitle() . "'. ($completedTutoriels tutoriel fini)");
        $utils = $event->getContainer()->get('app.utils');
        $utils->unlockAchievement($completedTutoriels . "_FINISHED_TUTORIALS", $event->getProgression()->getUser());
    }

}