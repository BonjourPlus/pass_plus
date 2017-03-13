<?php

namespace Jasdero\PassePlatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class OrdersFromSiteController extends Controller
{
    /**
     * Creates a new order entity from the site
     *
     * @Route("/admin/order/new", name="order_site_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm('Jasdero\PassePlatBundle\Form\OrdersType');
        $form->handleRequest($request);
        $products = [];
        if ($form->isSubmitted() && $form->isValid()) {
            //retrieving user
            $user = $form->get('user')->getData();
            //retrieving catalogs
            $catalogs = $form->get('catalogs')->getData();
            foreach ($catalogs as $catalog) {
                $products[] = $catalog->getId();
            }
            //creating order and recovering its id
            $orderId = $this->forward('JasderoPassePlatBundle:Orders:new', array(
                'user' => $user,
                'products' => $products
            ))->getContent();

            //displaying the new order
            return $this->redirectToRoute('orders_show', array('id' => $orderId));
        }

        return $this->render('orders/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}