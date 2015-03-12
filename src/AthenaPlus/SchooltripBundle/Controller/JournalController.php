<?php

namespace AthenaPlus\SchooltripBundle\Controller;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Journal;
use AthenaPlus\SchooltripBundle\Entity\Tab;
use AthenaPlus\SchooltripBundle\Form\JournalType;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Journal controller.
 *
 */
class JournalController extends Controller
{

    /**
     * Lists all Journal entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SchooltripBundle:Journal')->findAll();
        $trips = $em->getRepository('SchooltripBundle:Trip')->findAll();


        return $this->render('SchooltripBundle:Journal:index.html.twig', array(
            'entities' => $entities,
            'trips' => $trips
        ));
    }

    public function newAction(Request $request)
    {
        $numberOfTabs = $request->get('numberOfTabs');
        $trip_id = $request->get('trip');
        $title   = $request->get('journal-name');

        if(!is_numeric($numberOfTabs) OR $numberOfTabs<=0){
            throw new Exception("Number of tabs should be a number above 0!");
        }

        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository("SchooltripBundle:Trip")->find($trip_id);
        $journal = new Journal();

        $journal->setTrip($trip);
        $journal->setTitle($title);

        for($i=1; $i <= $numberOfTabs; $i++){
            $tab = new Tab();
            $journal->addTab($tab);
            $tab->setJournal($journal);
            $em->persist($tab);
            unset($tab);
        }
        $em->persist($journal);
        $em->flush();

        return $this->redirect($this->generateUrl('journal_edit', array("id"=>$journal->getId() )));

    }

    /**
     *
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $journal = $em->getRepository('SchooltripBundle:Journal')->find($id);

        if (!$journal) {
            throw $this->createNotFoundException('Unable to find Journal.');
        }

        $trip = $journal->getTrip();

        $sections = $trip->getSections();


        $mediaManager = $this->container->get('sonata.media.manager.media');
        $media = $mediaManager->findAll();

        return $this->render('SchooltripBundle:Journal:edit.html.twig', array(
            'journal' => $journal,
            'media'   => $media,
            'sections'=> $sections
        ));
    }


    /**
     *
     *
     */
    public function loadRecordHtmlAjaxAction(Request $request)
    {
        $id = $request->get('id');
        $remarks = $request->get('remarks');
        $time = $request->get('time');
        $tab_id = $request->get('tab_id');

        $em = $this->getDoctrine()->getManager();
        $record = $em->getRepository('SchooltripBundle:Entry')->find($id);

        if (!$record) {
            throw $this->createNotFoundException('Unable to find Journal.');
        }

        return $this->render('SchooltripBundle:Journal:recordDetail.html.twig', array(
            'record' => $record,
            'remarks' => $remarks,
            'tab_id' => $tab_id,
            'time' => $time
        ));
    }


    /**
     *
     *
     */
    public function storyTellerAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $journal = $em->getRepository('SchooltripBundle:Journal')->find($id);

        if (!$journal) {
            throw $this->createNotFoundException('Unable to find Journal entity.');
        }


        $records = $em->getRepository('SchooltripBundle:Entry')->findAll();


        $tabs = $journal->getTabs();
        foreach($tabs as $tab){
            //print_r($tab->getContent()); die;

        }

        $mediaManager = $this->container->get('sonata.media.manager.media');
        $media = $mediaManager->findAll();

        return $this->render('SchooltripBundle:Journal:storyTeller.html.twig', array(
            'journal'      => $journal,
            'media'        => $media,
            'full_records'      => $records
        ));
    }

    /**
     * Deletes a Journal entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SchooltripBundle:Journal')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Journal entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('journal'));
    }

    /**
     * Creates a form to delete a Journal entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('journal_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function saveTabAjaxAction(Request $request)
    {
        $date = $request->get('date');
        $id = $request->get('id');
        $contents = $request->get('contents');

        //print_r($date); die;
        $dateObject = new \DateTime($date);
        //print_r($dateObject); die;
        $em = $this->getDoctrine()->getManager();
        $tab = $em->getRepository('SchooltripBundle:Tab')->find($id);
        $tab->setDate($dateObject);
        $tab->setContent($contents);

        $em->persist($tab);
        $em->flush();
        return new Response("ok");
    }

    public function loadTabAjaxAction(Request $request)
    {

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $tab = $em->getRepository('SchooltripBundle:Tab')->find($id);

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $response = new Response($serializer->serialize($tab, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
