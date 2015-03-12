<?php

namespace AthenaPlus\SchooltripBundle\Controller;

use AthenaPlus\SchooltripBundle\Entity\Notification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Section;
use AthenaPlus\SchooltripBundle\Entity\Post;

use AthenaPlus\SchooltripBundle\Form\PostType;



class NotificationController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $notifications = $em->getRepository('SchooltripBundle:Notification')->findAll();


        return $this->render('SchooltripBundle:Base:notificationsFull.html.twig', array(
            'notifications' => $notifications
        ));

    }



}

