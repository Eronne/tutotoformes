<?php
/**
 * Created by IntelliJ IDEA.
 * User: Omar
 * Date: 01/09/2016
 * Time: 20:43
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TutorielController extends Controller
{


    /**
     * @Route("/tutoriel/git/{page}_{subpage}", name="git_tuto")
     */
    public function gitTutorielAction(Request $request, $page, $subpage){

        return $this->render('tutoriel/git_' . $page . '_' . $subpage . '.html.twig');

    }

}