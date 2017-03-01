<?php

namespace Jasdero\PassePlatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('JasderoPassePlatBundle:Default:index.html.twig');
    }

    //access to the admin section
    /**
     * @Route("/admin/index", name="admin")
     */
    public function adminIndexAction()
    {
        return $this->render('JasderoPassePlatBundle:Admin:indexAdmin.html.twig');
    }


}
