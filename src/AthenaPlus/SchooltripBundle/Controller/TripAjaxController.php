<?php

namespace AthenaPlus\SchooltripBundle\Controller;

use AthenaPlus\SchooltripBundle\Entity\ClassGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Trip;
use AthenaPlus\SchooltripBundle\Form\TripType;
use AthenaPlus\UserBundle\Entity\User;

/**
 * Trip controller.
 *
 */
class TripAjaxController extends Controller
{

    /**
     * update trip
     *
     */
    public function updateTripAction(Trip $trip,Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip->setTitle($request->get('title'));
        $trip->setDescription($request->get('description'));
        $trip->setDeparture($request->get('departure'));
        $trip->setDestination($request->get('destination'));


        // d/m/Y


        $dateFrom = \DateTime::createFromFormat('d/m/Y', $request->get('datefrom'));
        $dateTo = \DateTime::createFromFormat('d/m/Y', $request->get('dateto'));

        //$trip->setDateFrom(new \DateTime($request->get('datefrom')));
        $trip->setDateFrom($dateFrom);
        //$trip->setDateTo(new \DateTime($request->get('dateto')));
        $trip->setDateTo($dateTo);


        // reset groups
        $groups = $em->getRepository('SchooltripBundle:ClassGroup')->findAll();
        foreach ($groups as $group){
            if($groupTrip = $group->getTrip()){
                if($groupTrip->getId() == $trip->getId()){
                    $group->resetTrip();
                    $em->persist($group);
                    $em->flush();
                }
            }
        }

        if($request->get('groups')){
            foreach ($request->get('groups') as $group){
                $group = $em->getRepository('SchooltripBundle:ClassGroup')->find($group);
                $group->setTrip($trip);
                $em->persist($group);
                $em->flush();
            }
        }


        $em->persist($trip);
        $em->flush();

        return new Response('ok');
    }







    /**
     * load trip
     *
     */
    public function loadTripAction(Trip $trip)
    {

        $journalCreated = false;
        if($trip->getJournal()){
            $journalCreated = true;
        }

        $tripArray = array();
        $tripArray['title']             = $trip->getTitle();
        $tripArray['description']       = $trip->getDescription();
        $tripArray['destination']       = $trip->getDestination();
        $tripArray['departure']         = $trip->getDeparture();
        $tripArray['datefrom']          = $trip->getDateFrom('string');
        $tripArray['dateto']            = $trip->getDateTo('string');
        $tripArray['journalcreated']    = $journalCreated;

        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('SchooltripBundle:ClassGroup')->findAll();

        $groupsId = array();
        foreach ($groups as $group){
            if($groupTrip = $group->getTrip()){
                if($groupTrip->getId() == $trip->getId()){
                    $groupsId[] = $group->getId();
                }
            }
        }

        $tripArray['groups']   = $groupsId;
        return new JsonResponse($tripArray);
    }


    /**
     * new trip
     *
     */
    public function newTripAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = new Trip();
        $trip->setTitle($request->get('title'));
        $trip->setDescription($request->get('description'));
        $trip->setDeparture('');
        $trip->setDestination('');

        $em->persist($trip);
        $em->flush();
        return new Response("ok");
    }

    public function deleteTripAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('SchooltripBundle:Trip')->find($request->get('id'));

        $em->remove($trip);
        $em->flush();


        return new Response("ok");

    }


    /**
     * new trip
     *
     */
    public function getSectionsAction(Trip $trip, Request $request)
    {
        $sections = $trip->getSections();
        foreach($sections as $section){
            echo $section->getTitle();
        }
        die;
    }



    /**
     *
     *
     */
    public function updateNotificationSettingsAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->getUser();
        $settings = $request->get('settings');

        // do input validation
        // there should be 4 settings
        if (count($settings) != 2){
            throw new \Exception('Settings not in the correct format!');
        }

        $user->setNotificationSettings($settings);
        $em->persist($user);
        $em->flush();

        return new Response("ok");
    }

    /**
     *
     *
     */
    public function loadNotificationSettingsAction(Request $request)
    {
        $user = $this->getUser();
        $settings = $user->getNotificationSettings();

        return new JsonResponse($settings);
    }

    /**
     * todo: nog meegeven welke trip ?
     *
     */
    public function addNewGroupAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $group = new ClassGroup();
        $group->setName('group');
        $em->persist($group);
        $em->flush();

        $groups = $em->getRepository('SchooltripBundle:ClassGroup')->findAll();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');

        $response = new Response($serializer->serialize($groups, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }


    /**
     * todo: nog meegeven welke trip ?
     *
     */
    public function addNewUserAction(ClassGroup $group, Request $request)
    {

        $email = $request->get('email');
        $username = $request->get('username');

        //todo verify password
        $password  = $request->get('pass');
        $confirmedPassword = $request->get('confirm_pass');

        $factory = $this->get('security.encoder_factory');

        $user = new User();

        $encoder = $factory->getEncoder($user);
        $pass = $encoder->encodePassword($password, $user->getSalt());
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($pass);
        $user->setGroup($group);
        $user->setEnabled(true);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();


        // return user & password
        return new JsonResponse($this->formatUsers($group->getUsers()));

    }

    public function updateGroupAction(ClassGroup $group, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $name = $request->get('name');

        $group->setName($name);

        $em->persist($group);
        $em->flush();

        $groups = $em->getRepository('SchooltripBundle:ClassGroup')->findAll();

        return new JsonResponse($this->formatGroups($groups));
    }

    public function deleteGroupAction(ClassGroup $group)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $em->remove($group);
        $em->flush();

        $groups = $em->getRepository('SchooltripBundle:ClassGroup')->findAll();

        return new JsonResponse($this->formatGroups($groups));
    }


    public function updateUserAction(Request $request)
    {
        $email     = $request->get('email');
        $username  = $request->get('username');
        $password  = $request->get('pass');
        $confirmedPassword = $request->get('confirm_pass');
        $id = $request->get('user_id');


        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->find($id);


        if(($password==$confirmedPassword)&&strlen($password) > 3){
            $encoderService = $this->get('security.encoder_factory');
            $encoder = $encoderService->getEncoder($user);
            $encodedNewPass = $encoder->encodePassword($password, $user->getSalt());
            $user->setPassword($encodedNewPass);
        }

        $user->setUsername($username);
        $user->setEmail($email);

        $em->persist($user);
        $em->flush();

        return new Response("ok");
    }


    public function loadGroupAction(ClassGroup $group)
    {

        $groupArray = array();

        $groupArray["name"]  = $group->getName();
        $groupArray["id"]    = $group->getId();
        $groupArray["users"] = $group->getUsers();

        return new JsonResponse($groupArray);
    }

    public function loadUsersAction(ClassGroup $group)
    {
        $usersArray = $this->formatUsers($group->getUsers());

        return new JsonResponse($usersArray);
    }

    public function loadUserAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->find($id);
        $user = $this->formatUser($user);

        return new JsonResponse($user);
    }


    private function formatUsers($users){
        $usersArray = array();
        foreach ($users as $user){
           $usersArray[] = $this->formatUser($user);
        }

        return $usersArray;
    }

    private function formatUser(User $user){
        $group = $user->getGroup();

        $tempUser['name'] = $user->getUsername();
        $tempUser['id'] = $user->getId();
        $tempUser['email'] = $user->getEmail();
        $tempUser['group_id'] = $group->getId();

        return $tempUser;
    }

    private function formatGroups($groups){
        $groupsArray = array();
        foreach ($groups as $group){
            $groupsArray[] = $this->formatGroup($group);
        }

        return $groupsArray;
    }

    private function formatGroup(ClassGroup $group){
        $tempGroup = array();

        $tempGroup["name"]  = $group->getName();
        $tempGroup["id"]    = $group->getId();
        $tempGroup["users"] = $group->getUsers();

        return $tempGroup;
    }

    /**
     *
     *
     */
    public function loadGroupsAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        //$onlyShowAvailableTag = $request->get('available-tag');
        //$onlyShowAvailableTag = true;


        $groups = $em->getRepository('SchooltripBundle:ClassGroup')->findAll();

        $groups_array = array();
        foreach($groups as $group){
            $temp_group = array();

            /*if($onlyShowAvailableTag&&$group->getTrip()){
                continue;
            }*/
            $temp_group["name"]  = $group->getName();
            $temp_group["id"]    = $group->getId();
            $temp_group["users"] = $group->getUsers();
            $groups_array[] = $temp_group;
        }
        return new JsonResponse($groups_array);
    }




    public function updateProfileAction(Request $request)
    {
        $user = $this->getUser();

        $username    = trim($request->get('name'));
        $email       = trim($request->get('email'));
        $password    = trim($request->get('password'));
        $newPassword = trim($request->get('newPassword'));
        $newPasswordRepeat = trim($request->get('newPasswordRepeat'));

        $em = $this->getDoctrine()->getEntityManager();


        // Check if password needs to be changed
        if(!empty($newPassword)){
            // Password should be between 5 and 100 char long
            if(strlen($newPassword) <= 5 or strlen($newPassword) > 100){
                return new JsonResponse(array("status" => "NOK", "error" => "Passwords should be between 5 and 100 characters."));
            }
            // Check if passwords match
            if($newPassword !== $newPasswordRepeat){
                return new JsonResponse(array("status" => "NOK", "error" => "Passwords do not match"));
            }
        }

        // Check if password needs to be changed
        if ($user) {
            // Get the encoder for the users password
            $encoderService = $this->get('security.encoder_factory');
            $encoder = $encoderService->getEncoder($user);
            $encodedPass = $encoder->encodePassword($password, $user->getSalt());

            if ($user->getPassword() == $encodedPass) {

                if (!(filter_var($email, FILTER_VALIDATE_EMAIL))){
                    return new JsonResponse(array("status" => "NOK", "error" => "Not a valid email address. $email"));
                }
                if(empty($username)){
                    return new JsonResponse(array("status" => "NOK", "error" => "Username can't be empty."));
                }

                // password change
                if(!empty($newPassword)){
                    $encodedNewPass = $encoder->encodePassword($newPassword, $user->getSalt());
                    $user->setPassword($encodedNewPass);
                }
                try {
                    $user->setUsername($username);
                    $user->setEmail($email);
                    $em->persist($user);
                    $em->flush();
                }
                catch(Exception $e) {
                    return new JsonResponse(array("status"  => "NOK", "error" => $e->getMessage()));
                }

            } else {
                return new JsonResponse(array("status" => "NOK", "error" => "Password is not correct"));
            }
        } else {
            return new JsonResponse(array("status" => "NOK", "error" => "You are not logged in."));
        }

        return new JsonResponse(array("status" => "OK"));

    }

    /**
     * Show a list of notifications in the main toolbar specific for this user
     * @return Response html-templated list of max. 5 notifications
     */
    public function showNotificationsAction(){
        $user = $this->getUser();
        $repository = $this->getDoctrine()
            ->getRepository('SchooltripBundle:Notification');
        /* Only get notifications for this user, unless we're dealing with an admin */
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $q = $repository->createQueryBuilder('n')
                ->orderBy('n.created', 'DESC')
                ->setMaxResults(5)
                ->getQuery();
        } else {
            $q = $repository->createQueryBuilder('n')
                ->where('n.user = :user')
                ->setParameter('user', $user->getUsername())
                ->orderBy('n.created', 'DESC')
                ->setMaxResults(5)
                ->getQuery();
        }

        $notifications = $q->getResult();
        return $this->render('SchooltripBundle:Base:notificationList.html.twig', array(
            'notifications' => $notifications
        ));
    }
}