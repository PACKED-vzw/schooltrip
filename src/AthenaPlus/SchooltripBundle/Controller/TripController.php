<?php

namespace AthenaPlus\SchooltripBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Trip;
use AthenaPlus\SchooltripBundle\Form\TripType;

/**
 * Trip controller.
 *
 */
class TripController extends Controller
{

    /**
     * Lists all Trip entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SchooltripBundle:Trip')->findAll();
        $groups = $em->getRepository('SchooltripBundle:ClassGroup')->findAll();


        return $this->render('SchooltripBundle:Trip:index.html.twig', array(
            'entities' => $entities,
            'groups' => $groups
        ));
    }


    /**
     * Lists all Trip entities.
     *
     */
    public function overviewAction()
    {

        $em = $this->getDoctrine()->getManager();
        $trip = new Trip();

        $entities = $em->getRepository('SchooltripBundle:Trip')->findAll();

        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('trip'));
        }
        else {
            return $this->redirect($this->generateUrl('student_single_trip'));
        }
    }



    /**
     * Creates a new Trip entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Trip();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('trip_show', array('id' => $entity->getId())));
        }

        return $this->render('SchooltripBundle:Trip:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Trip entity.
    *
    * @param Trip $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Trip $entity)
    {
        $form = $this->createForm(new TripType(), $entity, array(
            'action' => $this->generateUrl('trip_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Trip entity.
     *
     */
    public function newAction()
    {
        $entity = new Trip();
        $form   = $this->createCreateForm($entity);

        return $this->render('SchooltripBundle:Trip:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Trip entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SchooltripBundle:Trip')->find($id);
        $mediaManager = $this->container->get('sonata.media.manager.media');
        $media = $mediaManager->findAll();



        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Trip entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SchooltripBundle:Trip:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'media'       => $media
        ));
    }

    /**
     * Displays a form to edit an existing Trip entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SchooltripBundle:Trip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Trip entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SchooltripBundle:Trip:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Trip entity.
    *
    * @param Trip $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Trip $entity)
    {
        $form = $this->createForm(new TripType(), $entity, array(
            'action' => $this->generateUrl('trip_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Trip entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SchooltripBundle:Trip')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Trip entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('trip_edit', array('id' => $id)));
        }

        return $this->render('SchooltripBundle:Trip:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Trip entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SchooltripBundle:Trip')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Trip entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('trip'));
    }

    /**
     * Creates a form to delete a Trip entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('trip_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }


    /**
     * Loads sections linked to a trip
     *
     */
    public function loadSectionsAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('SchooltripBundle:Trip')->find($id);

        return $this->render('SchooltripBundle:Trip:sections.html.twig', array(
            'sections'      => $trip->getSections(),

        ));

    }

}
