<?php

namespace AthenaPlus\SchooltripBundle\Controller;

use AthenaPlus\SchooltripBundle\Entity\ClassGroup;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Trip;
use AthenaPlus\SchooltripBundle\Entity\Section;
use AthenaPlus\SchooltripBundle\Form\TripType;

/**
 * Trip controller.
 *
 */
class ContentsAjaxController extends Controller
{
    /**
     * load sections
     *
     */
    public function loadSectionsAction(Trip $trip)
    {
        $sections = $trip->getSections();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $sectionsJson = $serializer->serialize($sections, "json");

        $response = new Response($sectionsJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * load section
     *
     */
    public function loadSectionAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $section = $em->getRepository('SchooltripBundle:Section')->find($id);


        $em->persist($section);
        $em->flush();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $sectionJson = $serializer->serialize($section, "json");

        $response = new Response($sectionJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * load challenge
     *
     */
    /*public function loadParametersAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $section = $em->getRepository('SchooltripBundle:Section')->find($id);


        $em->persist($section);
        $em->flush();*/
    public function loadParametersAction (Section $section) {
        $parameters = $section->getParameters();


        return new JsonResponse($parameters);
    }

    /**
     * load challenge
     *
     */
    public function saveParametersAction(Section $section, Request $request)
    {
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $em = $this->getDoctrine()->getManager();

            $method = $request->get('method');
            $data   = $request->get('data');

            switch($method) {
                case 'challenge':
                    $parameters = $section->getParameters();
                    $parameters['challenge'] = $data['challenge'];
                    $section->setParameters($parameters);
                    break;
                case 'link':
                    $parameters = $section->getParameters();
                    $parameters['link'] = array('label' => $data['label'], 'url' => $data['url']);
                    $section->setParameters($parameters);
                    break;
                case 'media':
                    break;
                default:
                    throw new \Exception('Not a correct parameters method!');

            }
            $em->persist($section);
            $em->flush();

            $container = $this->container;
            $serializer = $container->get('jms_serializer');
            $response = new Response($serializer->serialize($section, 'json'));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }

    /**
     * new section
     *
     */
    public function newSectionAction(Trip $trip)
    {

        $em = $this->getDoctrine()->getManager();

        $section = new Section();
        $section->setTitle(" ");
        $section->setType(" ");
        $section->setReady(false);
        $section->setDescription(" ");
        $section->setMedia(array());
        $section->setParameters(array());
        $section->setTrip($trip);

        $em->persist($section);
        $em->flush();

        $sections = $trip->getSections();

        $container    = $this->container;
        $serializer   = $container->get('jms_serializer');
        $sectionsJson = $serializer->serialize($sections, "json");

        $response = new Response($sectionsJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * save section
     *
     */
    public function saveSectionAction(Request $request)
    {
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $em = $this->getDoctrine()->getManager();
            /*if ($request->get('id')==0){
                $entity = new Section();
                $entity->setMedia = array();
                $entity->setParameters = array();
            }
            else {

            }*/


            $entity = $em->getRepository('SchooltripBundle:Section')->find($request->get('id'));

            $entity->setTitle($request->get('title'));
            $entity->setDescription($request->get('description'));
            $entity->setParameters($request->get('parameters'));
            $entity->setType($request->get('type'));
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
    }

    /**
     * delete section
     *
     */
    public function deleteSectionAction(Section $section)
    {
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $em = $this->getDoctrine()->getManager();
            try {
                $em->remove($section);
                $em->flush();
            }
            catch (Exception $e){
                return new Response("nok");
            }
            return new Response("ok");
        }
    }


    /**
     * add image
     *
     */
    public function addImageAction(Request $request)
    {
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $image     =  $request->get('image');
            $thumbnail =  $request->get('thumbnail');

            $em = $this->getDoctrine()->getEntityManager();
            $section = $em->getRepository('SchooltripBundle:Section')->find($request->get('section_id'));

            // create new media object
            $media = array(
                'filename' => $image,
                'thumbnail' => $thumbnail,
                'description' => '',
                'label' => $image,
                'id' => uniqid()
            );

            $section->addMedia($media);

            $em->persist($section);
            $em->flush();

            $response = new Response(json_encode($section->getMedia()));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }


    /**
     * load images
     *
     */
    public function loadImagesAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $section = $em->getRepository('SchooltripBundle:Section')->find($request->get('section_id'));


        $response = new Response(json_encode($section->getMedia()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * load image
     *
     */
    public function loadImageAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $section = $em->getRepository('SchooltripBundle:Section')->find($request->get('section_id'));

        $response = new Response(json_encode($section->getMediaObject($request->get('image_id'))));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }



    /**
     * Delete image from
     *
     */
    public function deleteImageAction(Request $request)
    {
        $imageId = $request->get('image_id');
        $em = $this->getDoctrine()->getEntityManager();
        $section = $em->getRepository('SchooltripBundle:Section')->find($request->get('section_id'));
        $section->removeMedia($imageId);

        $em->persist($section);
        $em->flush();

        $response = new Response(json_encode($section->getMedia()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * update image
     *
     */
    public function updateImageAction(Request $request)
    {
        if($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $imageId    =  $request->get('image_id');
            $description =  $request->get('description');
            $label       =  $request->get('label');

            $em = $this->getDoctrine()->getEntityManager();
            $section = $em->getRepository('SchooltripBundle:Section')->find($request->get('section_id'));
            $section->updateMediaObject($imageId, $description, $label);

            $em->persist($section);
            $em->flush();

            $response = new Response(json_encode($section->getMedia()));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
    }



}