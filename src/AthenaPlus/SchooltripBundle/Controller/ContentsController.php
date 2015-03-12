<?php

namespace AthenaPlus\SchooltripBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Section;
use AthenaPlus\SchooltripBundle\Entity\Entry;


/**
 * Contents controller.
 *
 */
class ContentsController extends Controller
{
    /**
     * Lists all Section entities.
     *
     */
    public function studentRecordsIndexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $trips = $em->getRepository('SchooltripBundle:Trip')->findAll();

        return $this->render('SchooltripBundle:Contents:records.html.twig', array(
            'trips' => $trips,
        ));
    }


    /**
     * Lists all Section entities.
     *
     */
    public function studentSectionsIndexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $trips = $em->getRepository('SchooltripBundle:Trip')->findAll();

        return $this->render('SchooltripBundle:Contents:sections.html.twig', array(
            'trips' => $trips,
        ));
    }


    /**
     * Shows detail page
     *
     */
    public function detailAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $trip = $em->getRepository('SchooltripBundle:Trip')->find($id);

        $mediaManager = $this->container->get('sonata.media.manager.media');
        $media = $mediaManager->findAll();

        return $this->render('SchooltripBundle:Contents:detail.html.twig', array(
            'trip' => $trip,
            'media'=> $media
        ));
    }

    /**
     * Access point for bookmarklets
     *
     */
    public function submitAction(Request $request)
    {
        $page = $request->get('page');

        return $this->render('SchooltripBundle:Contents:submit.html.twig', array(
            'page' => $page
        ));
    }


    public function loadSectionInformationAjaxAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('SchooltripBundle:Section')->find($request->get('id'));

        return $this->render('SchooltripBundle:Contents:sectionInformation.html.twig', array(
            'section' => $section
        ));

    }


    public function loadRecordListAjaxAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('SchooltripBundle:Section')->find($id);

        return $this->render('SchooltripBundle:Contents:recordList.html.twig', array(
            'records' => $section->getEntries()
        ));
    }


    public function loadRecordAjaxAction(Request $request){
        $id = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $entry = $em->getRepository('SchooltripBundle:Entry')->find($id);

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $response = new Response($serializer->serialize($entry, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    public function saveRecordAjaxAction(Request $request){
        $sectionId = $request->get('section_id');
        $recordId = $request->get('id');
        $em = $this->getDoctrine()->getManager();


        if($recordId==0){
            // create new
            $record = new Entry();
            $record->setUser($this->getUser()->getUsername());
            $record->setSection($em->getRepository('SchooltripBundle:Section')->find($sectionId));
        }
        else {
            //load from db
            $record = $em->getRepository('SchooltripBundle:Entry')->find($recordId);
        }

        $record->setTitle($request->get('title'));
        $record->setText($request->get('description'));
        $record->setItems($request->get('items'));

        $em->persist($record);
        $em->flush();

        return new Response('ok');
    }

    public function changeStateAjaxAction(Request $request){
        $state = $request->get('state');
        $id    = $request->get('id');
        $em    = $this->getDoctrine()->getManager();
        $section = $em->getRepository('SchooltripBundle:Section')->find($id);

        if($state=='true'){
            $section->setReady(true);
        }
        else {
            $section->setReady(false);
        }

        $em->persist($section);
        $em->flush();

        return new Response('ok');
    }

    public function getStateAjaxAction(Request $request){
        $id    = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('SchooltripBundle:Section')->find($id);
        if($section->getReady()){
            $ready = "true";
        }
        else {
            $ready = "false";
        }
        return new Response($ready);
    }
}
