<?php

namespace PassPlusBundle\Controller;

use PassPlusBundle\Entity\Orders;
use PassPlusBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orders = $em->getRepository('PassPlusBundle:Orders')->findAll();
        $products = $em->getRepository('PassPlusBundle:Product')->findAll();

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
     */
    public function newAction(Request $request)
    {
        $order = new Orders();

        //getting basic state to set products : part to improve
        $em = $this->getDoctrine()->getManager();
        $state = $em->getRepository('PassPlusBundle:State')->findOneBy(['id'=>1]);

        //generating form for customers
        $form = $this->createForm('PassPlusBundle\Form\OrdersType', $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //setting orders data
            $order->setDateCreation(new \DateTime());
            $order->setLastUpdate(new \DateTime());
            $order->setUser($this->getUser());

            $em->persist($order);
            $em->flush();

            //creating each product line
            $products = $form->get('catalogs')->getData();
            foreach ($products as $product)
            {
                $newProductLine = new Product();
                $newProductLine->setState($state);
                $newProductLine->setOrders($order);
                $newProductLine->setCatalog($product);
                $newProductLine->setPretaxPrice($product->getPretaxPrice());
                $newProductLine->setVatRate($product->getVat()->getRate());
                $em->persist($newProductLine);
                $em->flush();
            }

            //setting order status
            $this->get('orderStatus')->orderStatusAction($order);
            //back to index
            return $this->redirectToRoute('home');
        }

        return $this->render('orders/new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
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
        $products = $em->getRepository('PassPlusBundle:Product')->findBy(['orders'=>$order->getId()]);

        return $this->render('orders/show.html.twig', array(
            'order' => $order,
            'products'=>$products,
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
        $editForm = $this->createForm('PassPlusBundle\Form\OrdersType', $order);
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
            $em->flush($order);
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
            ->getForm()
        ;
    }
}
