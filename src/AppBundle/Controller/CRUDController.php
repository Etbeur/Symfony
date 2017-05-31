<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CRUD;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Crud controller.
 *
 * @Route("crud")
 */
class CRUDController extends Controller
{
    /**
     * Lists all cRUD entities.
     *
     * @Route("/", name="crud_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cRUDs = $em->getRepository('AppBundle:CRUD')->findAll();

        return $this->render('crud/index.html.twig', array(
            'cRUDs' => $cRUDs,
        ));
    }

    /**
     * Creates a new cRUD entity.
     *
     * @Route("/new", name="crud_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cRUD = new Crud();
        $form = $this->createForm('AppBundle\Form\CRUDType', $cRUD);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cRUD);
            $em->flush();

            return $this->redirectToRoute('crud_show', array('id' => $cRUD->getId()));
        }

        return $this->render('crud/new.html.twig', array(
            'cRUD' => $cRUD,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a cRUD entity.
     *
     * @Route("/{id}", name="crud_show")
     * @Method("GET")
     */
    public function showAction(CRUD $cRUD)
    {
        $deleteForm = $this->createDeleteForm($cRUD);

        return $this->render('crud/show.html.twig', array(
            'cRUD' => $cRUD,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing cRUD entity.
     *
     * @Route("/{id}/edit", name="crud_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CRUD $cRUD)
    {
        $deleteForm = $this->createDeleteForm($cRUD);
        $editForm = $this->createForm('AppBundle\Form\CRUDType', $cRUD);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('crud_edit', array('id' => $cRUD->getId()));
        }

        return $this->render('crud/edit.html.twig', array(
            'cRUD' => $cRUD,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a cRUD entity.
     *
     * @Route("/{id}", name="crud_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CRUD $cRUD)
    {
        $form = $this->createDeleteForm($cRUD);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cRUD);
            $em->flush();
        }

        return $this->redirectToRoute('crud_index');
    }

    /**
     * Creates a form to delete a cRUD entity.
     *
     * @param CRUD $cRUD The cRUD entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CRUD $cRUD)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('crud_delete', array('id' => $cRUD->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
