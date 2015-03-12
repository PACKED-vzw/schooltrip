<?php

namespace AthenaPlus\SchooltripBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Section;
use AthenaPlus\SchooltripBundle\Form\SectionType;

/**
 * Section controller.
 *
 */
class SectionController extends Controller
{

    /**
     * Lists all Section entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SchooltripBundle:Section')->findAll();

        return $this->render('SchooltripBundle:Section:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Section entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Section();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('section_show', array('id' => $entity->getId())));
        }

        return $this->render('SchooltripBundle:Section:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Section entity.
    *
    * @param Section $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Section $entity)
    {
        $form = $this->createForm(new SectionType(), $entity, array(
            'action' => $this->generateUrl('section_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Section entity.
     *
     */
    public function newAction()
    {
        $entity = new Section();
        $form   = $this->createCreateForm($entity);

        return $this->render('SchooltripBundle:Section:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Section entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SchooltripBundle:Section')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Section entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SchooltripBundle:Section:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Section entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SchooltripBundle:Section')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Section entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('SchooltripBundle:Section:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Section entity.
    *
    * @param Section $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Section $entity)
    {
        $form = $this->createForm(new SectionType(), $entity, array(
            'action' => $this->generateUrl('section_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Section entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SchooltripBundle:Section')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Section entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('section_edit', array('id' => $id)));
        }

        return $this->render('SchooltripBundle:Section:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Section entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SchooltripBundle:Section')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Section entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('section'));
    }

    /**
     * Creates a form to delete a Section entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('section_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    /**
     * saves an entity
     *
     */
    public function saveAjaxAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        if ($request->get('id')==0){
            $entity = new Section();
        }
        else {
            $entity = $em->getRepository('SchooltripBundle:Section')->find($request->get('id'));
        }

        $entity->setTitle($request->get('title'));
        $entity->setDescription($request->get('description'));
        $entity->setParameters($request->get('parameters'));
        $entity->setSources("test");
        $entity->setMedia($request->get('media'));
        $entity->setReady(false);


        // TODO: error handling
        $trip = $em->getRepository('SchooltripBundle:Trip')->find($request->get('tripid'));

        $entity->setTrip($trip);

        $em->persist($entity);
        $em->flush();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $response = new Response($serializer->serialize($entity, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function loadAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('SchooltripBundle:Section')->find($request->get('id'));

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $response = new Response($serializer->serialize($entity, 'json'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function galleryAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mediaManager = $this->container->get('sonata.media.manager.media');
        $media = $mediaManager->findAll();

        return $this->render('SchooltripBundle:Media:mediaPicker.html.twig', array(
            'media' => $media
        ));

    }




}
