<?php

namespace PassPlusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->render('PassPlusBundle:Default:index.html.twig');
    }

    //access to the admin section
    /**
     * @Route("/admin/index", name="admin")
     */
    public function adminIndexAction()
    {
		var_dump(get_browser($_SERVER['HTTP_USER_AGENT'], false)->device_type);
        return $this->render('PassPlusBundle:Admin:indexAdmin.html.twig');
    }


}
