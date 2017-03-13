<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Orders;
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
            $user = $form->get('user')->getData();
            $catalogs = $form->get('catalogs')->getData();
            foreach ($catalogs as $catalog) {
                $products[] = $catalog->getId();
            }

            $orderId = $this->forward('JasderoPassePlatBundle:Orders:new', array(
                'user' => $user,
                'products' => $products
            ))->getContent();


            return $this->redirectToRoute('orders_show', array('id' => $orderId));
        }

        return $this->render('orders/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
