<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Tutoriel;
use AppBundle\Entity\TutorielPage;
use AppBundle\Entity\Utilisateur;
use Doctrine\ORM\Query\Expr;

/**
 * TutorielPageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TutorielPageRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param $number
     * @return TutorielPage[]
     */
    public function findAllSuperiorToPage($number)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('tutorielPage')
            ->from('AppBundle:TutorielPage', 'tutorielPage')
            ->where($qb->expr()->gt('tutorielPage.pageNumber', $number))
            ->getQuery()->getResult();
    }

    /**
     * @param Tutoriel $tutoriel
     * @param Utilisateur $user
     * @return mixed
     */
    public function getLastUnreadedSlug(Tutoriel $tutoriel, Utilisateur $user){
        $qb = $this->getEntityManager()->createQueryBuilder();
        /** @var TutorielPage[] $tutorielPages */
        $tutorielPages = $qb->select('tutoriel_page')
            ->from('AppBundle:TutorielPage', 'tutoriel_page')
            ->innerJoin('tutoriel_page.tutoriel', 'tutoriel')
            ->where('tutoriel = :tutoriel')
            ->setParameter('tutoriel', $tutoriel)
            ->getQuery()
            ->getResult();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $cp = $qb->select('user_progression.completedPages')
            ->from('AppBundle:UserProgression', 'user_progression')
            ->innerJoin('user_progression.tutoriel', 'tutoriel')
            ->innerJoin('user_progression.user', 'user')
            ->where('user = :user')
            ->setParameter('user', $user)
            ->andWhere('tutoriel = :tutoriel')
            ->setParameter('tutoriel', $tutoriel)
            ->getQuery()
            ->getResult()[0];
        $tpslugs = [];
        foreach ($tutorielPages as $tutorielPage){
            $tpslugs[] = $tutorielPage->getSlug();
        }
        $final = array_diff($tpslugs, $cp['completedPages']);
        return end($final);

    }


}
