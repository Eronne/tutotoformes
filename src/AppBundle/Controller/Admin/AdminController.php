<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 * @package AppBundle\Controller\Admin
 * @Route("/admin")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="admin_index")
     */
    public function indexAction(Request $request)
    {
        return $this->render("admin/index.html.twig");
    }
}
