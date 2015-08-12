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



class CommunityController extends Controller
{

    public function loadAction(Request $request){
        $offset = $request->get('offset');
        $number = $request->get('number');
        return $this->createJsonResponse(true, $number, $offset);
    }

    /**
     * Creates a form to create a Post entity.
     *
     * @param Post $post
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Post $post)
    {
        $form = $this->createForm(new PostType(), $post, array(
            'action' => $this->generateUrl('community_index'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Send message', 'attr' => array('class' => 'btn btn-submit-post')));

        return $form;
    }



    private function createJsonResponse($success, $number = 10, $offset = 0){
        $em = $this->getDoctrine()->getManager();


        $posts = $em->getRepository('SchooltripBundle:Post')->findLast($number, $offset);
        $result = Array();


        $result['success'] = true;
        $result['posts'] = $this->renderView('SchooltripBundle:Community:posts.html.twig', array(
            'posts'      => $posts,

        ));
        $result['length'] = count($posts);

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        /* Empty form */
        $post = new Post();
        $postForm = $this->createCreateForm($post);
        /* If the request is POST, a new item on the board was created */
        if ($request->isMethod('POST')) {
            $postForm->handleRequest($request);
            if ($postForm->isValid()) {
                $em->persist($post);
                $em->flush();
                $notif = new Notification();
                $notif->setType("Post");
                $notif->setUser($user);
                $notif->setContentId($post->getId());
                $url = $this->container->get('router')->generate(
                    'community_index'
                );
                $message = "<span class='fat'>{$this->getUser()}</span> placed a <a href='$url'>message</a> in the <a href='$url'>notification section.</a> ";
                $notif->setMessage($message);
                $em->persist($notif);
                $em->flush();
                return $this->createJsonResponse(true);
            } else {
                $response['success'] = false;
                return new JsonResponse($response);
            }
        }
        $group = $user->getGroup();
        if ($group) {
            $posts = $em->getRepository('SchooltripBundle:Post')->findLast();
        } else {
            /* Admins are not in a group, so show all posts */
            $posts = $em->getRepository('SchooltripBundle:Post')->findLast();
        }
        return $this->render('SchooltripBundle:Community:index.html.twig', array(
            'posts' => $posts,
            'post' => $post,
            'post_form'   => $postForm->createView(),
        ));

    }


}

