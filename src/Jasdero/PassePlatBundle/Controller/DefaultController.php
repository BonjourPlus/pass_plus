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
     * @Route("/admin/dashboard", name="dashboard")
     */
    public function adminIndexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $states = $em->getRepository('JasderoPassePlatBundle:State')->findBy([], ['weight' => 'DESC']);
        $products = $em->getRepository('JasderoPassePlatBundle:Product')->findAll();
        $orders = $em->getRepository('JasderoPassePlatBundle:Orders')->findAll();


        return $this->render('JasderoPassePlatBundle:Admin:dashboard.html.twig', array(
            'states' => $states,
            'products' => $products,
            'orders' => $orders,

        ));

    }


}
