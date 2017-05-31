<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Form;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Form controller.
 *
 * @Route("formCrud")
 */
class FormController extends Controller
{
    /**
     * Lists all form entities.
     *
     * @Route("/", name="formCrud_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $forms = $em->getRepository('AppBundle:Form')->findAll();

        return $this->render('form/index.html.twig', array(
            'forms' => $forms,
        ));
    }

    /**
     * Creates a new form entity.
     *
     * @Route("/new", name="formCrud_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $form = new Form();
        $form = $this->createForm('AppBundle\Form\FormType', $form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form);
            $em->flush();

            return $this->redirectToRoute('formCrud_show', array('id' => $form->getId()));
        }

        return $this->render('form/new.html.twig', array(
            'form' => $form,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a form entity.
     *
     * @Route("/{id}", name="formCrud_show")
     * @Method("GET")
     */
    public function showAction(Form $form)
    {
        $deleteForm = $this->createDeleteForm($form);

        return $this->render('form/show.html.twig', array(
            'form' => $form,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing form entity.
     *
     * @Route("/{id}/edit", name="formCrud_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Form $form)
    {
        $deleteForm = $this->createDeleteForm($form);
        $editForm = $this->createForm('AppBundle\Form\FormType', $form);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('formCrud_edit', array('id' => $form->getId()));
        }

        return $this->render('form/edit.html.twig', array(
            'form' => $form,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a form entity.
     *
     * @Route("/{id}", name="formCrud_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Form $form)
    {
        $form = $this->createDeleteForm($form);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($form);
            $em->flush();
        }

        return $this->redirectToRoute('formCrud_index');
    }

    /**
     * Creates a form to delete a form entity.
     *
     * @param Form $form The form entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Form $form)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('formCrud_delete', array('id' => $form->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
