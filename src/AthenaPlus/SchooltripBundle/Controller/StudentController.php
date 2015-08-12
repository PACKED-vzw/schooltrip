<?php


namespace AthenaPlus\SchooltripBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AthenaPlus\SchooltripBundle\Entity\Section;
use AthenaPlus\SchooltripBundle\Entity\Entry;
use AthenaPlus\SchooltripBundle\Entity\Journal;
use AthenaPlus\SchooltripBundle\Entity\Notification;


/**
 * Student controller.
 *
 */
class StudentController extends Controller
{


    /**
     * Load records for section
     *
     */
    public function loadRecordsAction(Section $section, Request $request)
    {
        $entries = $section->getEntries();


        $records = array();

        foreach($entries as $entry){
            $record = array();
            $record['title'] = $entry->getTitle();
            $record['items'] = $entry->getItems();
            $record['description']  = $entry->getText();
            $record['id']    = $entry->getId();
            $record['url']    = $entry->getUrl();
            $record['user']  = $entry->getUser();
            $records[] = $record;
        }
        $container = $this->container;
        $serializer = $container->get('jms_serializer');


        $response = new Response($serializer->serialize($records, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Load record
     *
     */
    public function loadRecordAction(Entry $entry)
    {
        $record['title'] = $entry->getTitle();
        $record['items'] = $entry->getItems();
        $record['description']  = $entry->getText();
        $record['id']    = $entry->getId();
        $record['url']    = $entry->getUrl();
        $record['user']  = $entry->getUser();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');

        $response = new Response($serializer->serialize($record, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * save record
     *
     */
    public function saveRecordAction(Entry $entry, Request $request)
    {

        $entry->setTitle($request->get('title'));
        $entry->setUrl($request->get('url'));
        $entry->setText($request->get('description'));



        $em = $this->getDoctrine()->getManager();
        $em->persist($entry);
        $em->flush();

        return new Response("ok");


    }

    /**
     * Mark section as ready
     *
     */
    public function markSectionAsReadyAction(Section $section)
    {
        $em = $this->getDoctrine()->getManager();
        $section->setReady(!($section->getReady()));

        $em->persist($section);
        $em->flush();

        return new Response("ok");
    }

    /**
     * add image
     *
     */
    public function addImageAction(Request $request)
    {
            $type      =  ($request->get('type') ? $request->get('type') : 'image');
            $description = "";
            $link = "";

            $image     =  $request->get('image');
            $thumbnail =  $request->get('thumbnail');
            $label = $image;
            if($type=="europeana"){
                $description = $request->get('description');
                $link = $request->get('link');
                $label = $request->get('title');
            }



            $em = $this->getDoctrine()->getEntityManager();
            $entry = $em->getRepository('SchooltripBundle:Entry')->find($request->get('record_id'));

            // create new media object
            $media = array(
                'filename' => $image,
                'thumbnail' => $thumbnail,
                'description' => $description,
                'link' => $link,
                'type' => $type,
                'label' => $label,
                'id' => uniqid()
            );

            $entry->addMedia($media);

            $em->persist($entry);
            $em->flush();

            $response = new Response(json_encode($entry->getMedia()));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

    }


    /**
     * load images
     *
     */
    public function loadImagesAction(Request $request)
    {

        $em = $this->getDoctrine()->getEntityManager();
        $entry = $em->getRepository('SchooltripBundle:Entry')->find($request->get('record_id'));


        $response = new Response(json_encode($entry->getMedia()));
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
        $entry = $em->getRepository('SchooltripBundle:Entry')->find($request->get('record_id'));

        $response = new Response(json_encode($entry->getMediaObject($request->get('image_id'))));
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
        $entry = $em->getRepository('SchooltripBundle:Entry')->find($request->get('record_id'));
        $entry->removeMedia($imageId);

        $em->persist($entry);
        $em->flush();

        $response = new Response(json_encode($entry->getMedia()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
     * new record
     *
     */
    public function newRecordAction(Section $section)
    {

        $record = new Entry();
        $record->setUser($this->container->get('security.context')->getToken()->getUser());
        $record->setSection($section);

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($record);

        $em->flush();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');

        $records = array();

        foreach($section->getEntries() as $entry){
            $record = array();
            $record['title'] = $entry->getTitle();
            $record['items'] = $entry->getItems();
            $record['description']  = $entry->getText();
            $record['id']    = $entry->getId();
            $record['url']    = $entry->getUrl();
            $record['user']  = $entry->getUser();
            $records[] = $record;
        }


        $response = new Response($serializer->serialize($records, 'json'));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    public function journalRecordsAction($id)
    {

        // todo: DRY it
        if ($id == 0){
            $user = $this->getUser();
            $group = $user->getGroup();
            $trip = $group->getTrip();
            $id = $trip->getId();
        }
        else {
            if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
                throw new \Exception("You can't access this trip!");
            }
        }

        $em = $this->getDoctrine()->getManager();
        $trip = $em->getRepository('SchooltripBundle:Trip')->find($id);


        $dateFrom = $trip->getDateFrom();
        $dateTo = $trip->getDateTo();
        //$diff = $dateFrom->diff($dateTo)->format("%a");

        if (!$trip->getJournal()){
        //if(true){
            $journal = new Journal();
            $journal->setPublished(false);
            $journal->setTrip($trip);
            $iteratorDate = clone $dateFrom;

            $datesArray = array();

            // prepare dates array
            $i = 1;
            while( $iteratorDate != $dateTo ){
                $datesArray[$i] = array( "date" => $iteratorDate,
                                         "dateString" => $iteratorDate->format('d/m/Y'),
                                         "entries" =>
                                           array()
                                         );
                $iteratorDate->modify('+1 day');
                $i++;
            }
            $journal->setDates($datesArray);

            $em->persist($journal);
            $em->flush();
        }
        else {
            $journal = $trip->getJournal();
            //$journal = $journals[0];

        }



        $sections = $trip->getSections();


        return $this->render('SchooltripBundle:Student:journalRecords.html.twig', array(
            'trip' => $trip,
            'journal' => $journal,
            'sections' => $sections
        ));

    }



    private function formatDateTab($dateTab){
       // print_r($dateTab); die;

        $em = $this->getDoctrine()->getEntityManager();

        foreach($dateTab['entries'] as &$entry){
            $record = $em->getRepository('SchooltripBundle:Entry')->find($entry['original_record_id']);
            if ($record && is_object($record)) {
                $entry['title'] = $record->getTitle();
                $entry['user']  = $record->getUser();
                $entry['text']  = $record->getText();
            }
            unset($record);
        }

        //print_r($dateTab); die;
        return $dateTab;
    }


    public function addJournalEntryAction(Journal $journal, Request $request)
    {
        $dates = $journal->getDates();
        $dateId = $request->get('date_id');

        $id = uniqid();

        $newEntry = array(
            "original_record_id" => $request->get('original_record_id'),
            'hour' => "00:00",
            'id'  => $id

        );

        $dates[$dateId]["entries"][$id] = $newEntry;
        $journal->setDates($dates);

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($journal);
        $em->flush();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');

        $dateJson = $serializer->serialize($this->formatDateTab($dates[$dateId]), "json");

        $response = new Response($dateJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function editTimeJournalEntryAction(Journal $journal, Request $request)
    {
        $dates = $journal->getDates();
        $dateId = $request->get('date_id');
        $entryId = $request->get('date_entry_id');
        $hour = $request->get('hour');


        $dates[$dateId]["entries"][$entryId]["hour"] = $hour;

        $journal->setDates($dates);

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($journal);
        $em->flush();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');

        $dateJson = $serializer->serialize($this->formatDateTab($dates[$dateId]), "json");

        $response = new Response($dateJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }



    public function updateJournalEntryAction(Journal $journal, Request $request)
    {

        $dates = $journal->getJournalDates();
        $dateId = $request->get('date_tab_id');
        $entryId = $request->get('entry_id');
        $title = $request->get('title');
        $description = $request->get('description');
        $hour = $request->get('hour');
        $extraDescription = $request->get('extra_description');
        $extraTitle = $request->get('extra_title');


        //echo("hallo");
        //print_r($entryId); die;
        $dates[$dateId]["entries"][$entryId]["hour"]    = $hour;
        $dates[$dateId]["entries"][$entryId]["title"]   = $title;
        //$dates[$dateId]["entries"][$entryId]["id"]   = $entryId;
        //$dates[$dateId]["entries"][$entryId]["extra"]   = $title;
        $dates[$dateId]["entries"][$entryId]["text"]    = $description;
        $dates[$dateId]["entries"][$entryId]["extra_description"] = $extraDescription;
        $dates[$dateId]["entries"][$entryId]["extra_title"]       = $extraTitle;

        //print_r($dates); die;


        $journal->setJournalDates($dates);

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($journal);
        $em->flush();


        //print_r($journal->getDatesJournal()); die;

        return new Response("ok");
    }

    public function loadDateAction(Journal $journal, Request $request)
    {
        $dates = $journal->getDates();
        $dateId = $request->get('date_id');

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $dateJson = $serializer->serialize($this->formatDateTab($dates[$dateId]), "json");

        $response = new Response($dateJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function deleteJournalEntryAction(Journal $journal, Request $request)
    {
        $id = $request->get('entry_id');
        $dates = $journal->getDates();
        $dateId = $request->get('date_id');

        unset($dates[$dateId]["entries"][$id]);

        $journal->setDates($dates);

        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($journal);
        $em->flush();

        $container = $this->container;
        $serializer = $container->get('jms_serializer');
        $dateJson = $serializer->serialize($this->formatDateTab($dates[$dateId]), "json");

        $response = new Response($dateJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;


    }

    private static function timeSort($a, $b) {
        $a = $a['hour'];
        $b = $b['hour'];
        if ($a == $b)
            return 0;
        return ($b > $a) ? -1 : 1;
    }


    public function clearJournalAction(Journal $journal){

        // check if user can empty this journal
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();
            $group = $user->getGroup();
            $trip = $group->getTrip();
            $journalTrip = $trip->getJournal();
            if($journal->getId() != $journalTrip->getId()){
                throw new \Exception("Insufficient rights");
            }
        }

        $journal->setJournalDates(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($journal);
        $em->flush();

        $trip = $journal->getTrip();

        return $this->redirect($this->generateUrl('student_records_index', array('id' => $trip->getId())));
    }

    public function evaluationRequestAction(Journal $journal){

        // check if user can empty this journal
        $user = $this->getUser();

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {

            $group = $user->getGroup();
            $trip = $group->getTrip();
            $journalTrip = $trip->getJournal();
            if($journal->getId() != $journalTrip->getId()){
                throw new \Exception("Insufficient rights");
            }
        }

        $date = new \DateTime('now');

        $eval = array("user" => $user, "time" => $date->format('d-m-Y'));




        $journal->setEvaluationRequest($eval);
        $em = $this->getDoctrine()->getManager();
        $em->persist($journal);

        $trip = $journal->getTrip();
        $notif = new Notification();
        $notif->setType("evaluation");
        $notif->setUser($this->getUser());
        $notif->setContentId($trip->getId());

        $url = $this->container->get('router')->generate(
            'student_records_index', array('id' => $trip->getId())
        );

        $message = "<span class='fat'>{$this->getUser()}</span> made a <a href='$url'>evaluation request</a> for trip <a href='$url'>{$trip->getTitle()}.</a> ";

        $notif->setMessage($message);

        $em->persist($notif);
        $em->flush();



        return $this->redirect($this->generateUrl('student_records_index', array('id' => $trip->getId())));
    }

    /**
     * Get a trip from the db. Either the trip is identified by $id (only for users with ROLE_ADMIN)
     * or the trip is linked to the user via the group he/she is in.
     * @param null $id
     * @return object
     * @throws \Exception
     */
    protected function get_trip ($id = null) {
        $user = $this->getUser();
        if ($id !== null) {
            /* Users only have access to their own trips ($id = 0) - Admins may access all */
            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                throw new \Exception ('Only admins can access trips outside of their group(s)!');
            }
            $em = $this->getDoctrine()->getManager();
            $trip = $em->getRepository('SchooltripBundle:Trip')->findOneBy(array('id' => $id));
        } else {
            $group = $user->getGroup();
            $trip = $group->getTrip();
        }
        return $trip;
    }

    /**
     * Controller for a finished journal
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function finishedJournalAction($id) {
        if ($id === 0) {
            $id = null;
        }
        $trip = $this->get_trip($id);
        $journal  = $trip->getJournal();
        $sections = $trip->getSections();

        return $this->render('SchooltripBundle:Student:finishedJournal.html.twig', array(
            'trip'     => $trip,
            'journal'  => $journal,
            'sections' => $sections
        ));
    }

    /**
     * Fetch a single trip ($id) from the DB and parse it in a twig template that is returned.
     * @param $id
     * @return String
     * @throws \Exception
     */
    public function singleTripAction($id) {
        if ($id === 0) {
            $id = null;
        }
        $trip = $this->get_trip($id);
        $tripSectionsReady = false;
        foreach($trip->getSections() as $section){
            if ($section->getReady()) {
                $tripSectionsReady = true;
                break;
            }
        }
        return $this->render('SchooltripBundle:Student:singleTrip.html.twig', array(
            'trip' => $trip,
            'tripReady' => $tripSectionsReady
        ));
    }

    /**
     * Controller for the finalise journal action. Returns a parsed twig template with a journal (the journal is dependent on $mode)
     * @param $id
     * @param $mode
     * @return Response
     * @throws \Exception
     */
    public function finaliseJournalAction ($id, $mode) {
        if ($id === 0) {
            $id = null;
        }
        $trip = $this->get_trip($id);
        $journal  = $trip->getJournal();
        $preview = false;
        if ($mode == 'preview') {
            $preview = true;
        }
        /* If $journal->getJournalDates() is trueish, prepare for finalisation. Else, do nothing to the journal. */
        if (!$journal->getJournalDates()) {
            $journal = $this->prepareJournalForFinalisation($journal, $preview);
        }
        $sections = $trip->getSections();

        return $this->render('SchooltripBundle:Student:finaliseJournal.html.twig', array(
            'trip'     => $trip,
            'journal'  => $journal,
            'sections' => $sections,
            'mode'     => $mode
        ));
    }

    /**
     * Prepare a "final" version of the journal, either for preview or for persistence (preview = false) in the DB
     * @param $journal
     * @param bool|false $preview
     * @return object
     */
    private function prepareJournalForFinalisation ($journal, $preview = false){
        // 1. datetabs bestaan uit entries (met tijd enzo)
        //    deze entries hebben een original_record_id
        //    aangezien dit finaal is kunnen we nu alle info uit het record kopiëren
        $em = $this->getDoctrine()->getEntityManager();

        $dateTabs = $journal->getDates();

        /*
         * Copy all information from the DB to the journal (as this a finalisation, no further changes are expected)
         */
        foreach($dateTabs as &$dateTab){
            foreach($dateTab['entries'] as &$entry){
                $record = $em->getRepository('SchooltripBundle:Entry')->find($entry['original_record_id']);
                $entry['title'] = $record->getTitle();
                $entry['user']  = $record->getUser();
                $entry['text']  = $record->getText();
                $entry['url']  = $record->getUrl();
                $entry['extra_title']  = '';
                $entry['extra_description']  = '';

                $images = $record->getMedia();

                if (count($images) > 0){
                    $entry['primary_image'] = array_shift($images);
                    $entry['other_images']  = $images;
                }
                else {
                    $entry['primary_image'] = null;
                    $entry['other_images'] = array();
                }
                unset($record);
            }
        }

        $journal->setJournalDates($dateTabs);
        if (!$preview){
            $em->persist($journal);
            $em->flush();
        }
        return $journal;

    }

}
