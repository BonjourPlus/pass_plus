<?php

namespace Jasdero\PassePlatBundle\Controller;

use Jasdero\PassePlatBundle\Entity\Catalog;
use Jasdero\PassePlatBundle\Entity\Orders;
use Jasdero\PassePlatBundle\Entity\Product;
use Jasdero\PassePlatBundle\Entity\State;
use Jasdero\PassePlatBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Order controller.
 *
 */
class OrdersController extends Controller
{
    /**
     * Lists all order entities.
     *
     * @Route("/admin/orders/", name="orders_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');


        $queryBuilder = $em->getRepository('JasderoPassePlatBundle:Orders')->createQueryBuilder('o');
        $query = $queryBuilder->getQuery();

        $orders = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 10)/*limit per page*/
        );

        $products = $em->getRepository('JasderoPassePlatBundle:Product')->findAll();

        return $this->render('orders/index.html.twig', array(
            'orders' => $orders,
            'products' => $products,
        ));
    }

    /**
     * Creates a new order entity.
     *
     * @Route("orders/new", name="orders_new")
     * @Method({"GET", "POST"})
     * @param User $user an authenticated user
     * @param array $products an array of ordered products
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    //currently  using next method to create orders during dev

    public function newAction(User $user, array $products)
    {
        $order = new Orders();

        //getting basic state to set products : part to improve
        $em = $this->getDoctrine()->getManager();
        $state = $em->getRepository('JasderoPassePlatBundle:State')->findOneBy(['id' => 1]);


        //setting orders data
        $order->setDateCreation(new \DateTime());
        $order->setUser($user);
        $em->persist($order);
        $em->flush();

        //creating each product line

        foreach ($products as $product) {
            $catalog = $em->getRepository('JasderoPassePlatBundle:Catalog')->findOneBy(['id'=>$product]);

            $newProductLine = new Product();
            $newProductLine->setState($state);
            $newProductLine->setOrders($order);
            $newProductLine->setCatalog($catalog);
            $newProductLine->setPretaxPrice($catalog->getPretaxPrice());
            $newProductLine->setVatRate($catalog->getVat()->getRate());
            $em->persist($newProductLine);
            $em->flush();
        }

        //setting order status
        $this->get('orderstatus')->orderStatusAction($order);


        //give back the new order id to update on the file
        return New Response ($order->getId());

    }

    /**
     * Finds and displays a order entity.
     *
     * @Route("/admin/orders/{id}", name="orders_show")
     * @Method("GET")
     */
    public function showAction(Orders $order)
    {
        $deleteForm = $this->createDeleteForm($order);

        //getting products contained inside the order
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('JasderoPassePlatBundle:Product')->findBy(['orders' => $order->getId()]);

        return $this->render('orders/show.html.twig', array(
            'order' => $order,
            'products' => $products,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing order entity.
     *
     * @Route("/admin/orders/{id}/edit", name="orders_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Orders $order)
    {
        $deleteForm = $this->createDeleteForm($order);
        $editForm = $this->createForm('Jasdero\PassePlatBundle\Form\OrdersType', $order);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('orders_show', array('id' => $order->getId()));
        }

        return $this->render('orders/edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a order entity.
     *
     * @Route("/admin/orders/{id}", name="orders_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Orders $order)
    {
        $form = $this->createDeleteForm($order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();
        }

        return $this->redirectToRoute('orders_index');
    }

    /**
     * Creates a form to delete a order entity.
     *
     * @param Orders $order The order entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Orders $order)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('orders_delete', array('id' => $order->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    //orders sorted by status from the statuses page
    /**
     * @Route("/admin/orders/status/{id}", name="orders_by_status")
     */
    public function ordersByStatusAction(State $state)
    {
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository('JasderoPassePlatBundle:Orders')->findBy(['state' => $state->getId()]);
        $products = $em->getRepository('JasderoPassePlatBundle:Product')->findAll();

        return $this->render(':orders:ordersFiltered.html.twig', array(
            'orders' => $orders,
            'products' => $products,
        ));
    }

    //orders filtered by catalog
    /**
     *
     * @Route("/admin/orders/catalog/{id}", name="orders_by_catalog")
     * @param Catalog $catalog
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ordersByCatalogAction(Catalog $catalog)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('JasderoPassePlatBundle:Product')->findAll();

        //getting Orders Id
        $ordersId = $em->getRepository('JasderoPassePlatBundle:Product')->findOrderByCatalog($catalog);
        //getting Orders
        $orders = [];
        foreach ($ordersId as $order) {
            $orders[] = $em->getRepository('JasderoPassePlatBundle:Orders')->findOneBy(['id' => $order]);
        }

        return $this->render('orders/ordersFiltered.html.twig', array(
            'orders' => $orders,
            'products' => $products,
        ));
    }

}
