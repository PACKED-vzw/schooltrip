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



class NotificationController extends Controller {

    /**
     * Function that returns a list of notifications for a given user
     * @param Request $request
     * @return Response a list of html-templated notifications
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $repository = $em->getRepository('SchooltripBundle:Notification');
        /* Only get notifications created by this user, unless we're dealing with an admin */
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $q = $repository->createQueryBuilder('n')->getQuery();
        } else {
            $q = $repository->createQueryBuilder('n')->where('n.user = :user')->setParameter('user', $user->getUsername())->getQuery();
        }
        $notifications = $q->getResult();
        return $this->render('SchooltripBundle:Base:notificationsFull.html.twig', array(
            'notifications' => $notifications
        ));

    }



}

