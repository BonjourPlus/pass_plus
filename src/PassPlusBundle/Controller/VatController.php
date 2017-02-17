<?php

namespace PassPlusBundle\Controller;

use PassPlusBundle\Entity\Vat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Vat controller.
 *
 * @Route("vat")
 */
class VatController extends Controller
{
    /**
     * Lists all vat entities.
     *
     * @Route("/", name="vat_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $vats = $em->getRepository('PassPlusBundle:Vat')->findAll();

        return $this->render('vat/index.html.twig', array(
            'vats' => $vats,
        ));
    }

    /**
     * Creates a new vat entity.
     *
     * @Route("/new", name="vat_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $vat = new Vat();
        $form = $this->createForm('PassPlusBundle\Form\VatType', $vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vat);
            $em->flush($vat);

            return $this->redirectToRoute('vat_show', array('id' => $vat->getId()));
        }

        return $this->render('vat/new.html.twig', array(
            'vat' => $vat,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a vat entity.
     *
     * @Route("/{id}", name="vat_show")
     * @Method("GET")
     */
    public function showAction(Vat $vat)
    {
        $deleteForm = $this->createDeleteForm($vat);

        return $this->render('vat/show.html.twig', array(
            'vat' => $vat,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing vat entity.
     *
     * @Route("/{id}/edit", name="vat_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Vat $vat)
    {
        $deleteForm = $this->createDeleteForm($vat);
        $editForm = $this->createForm('PassPlusBundle\Form\VatType', $vat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('vat_edit', array('id' => $vat->getId()));
        }

        return $this->render('vat/edit.html.twig', array(
            'vat' => $vat,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a vat entity.
     *
     * @Route("/{id}", name="vat_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Vat $vat)
    {
        $form = $this->createDeleteForm($vat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vat);
            $em->flush($vat);
        }

        return $this->redirectToRoute('vat_index');
    }

    /**
     * Creates a form to delete a vat entity.
     *
     * @param Vat $vat The vat entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Vat $vat)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vat_delete', array('id' => $vat->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
