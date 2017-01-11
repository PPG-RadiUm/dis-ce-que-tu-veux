<?php

namespace AppBundle\Controller;

use Guzzle\Http\Message\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Room;
use AppBundle\Entity\Player;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="dcqtv_homepage")
     */
    public function indexAction(Request $request)
    {
        /*$user = $this->container->get('fos_user.user_manager')
            ->findUserByUsername('val');

        var_dump($user);die;*/

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/play", name="dcqtv_play")
     */
    public function playAction(Request $request)
    {
        return $this->render('default/play.html.twig');
    }

    /**
     * @Route("/lobby_configuration", name="dcqtv_lobby_configuration")
     */
    public function lobbyConfigurationAction(Request $request)
    {
        return $this->render('default/lobby_configuration.html.twig', ["host" => 0]);
    }

    /**
     * @Route("/lobby", name="dcqtv_lobby")
     */
    public function lobbyAction(Request $request)
    {

        if(!isset($rooms_max_id)){
            $rooms_max_id = -1;
        }

        if(!isset($rooms)){
            $rooms = array();
        }

        if(!isset($players)){
            $players = array();
            $players[0] = new Player(0, 'test', "room_waiting");
        }

        if($request->getMethod() == 'POST'){
            $data = $request->request->all();

            // Problème de persistence côté serveur, voir une soltuion reactPHP, Redis, MySQL ou PHPDM ??
            $rooms_max_id++;
            $room = new Room($rooms_max_id, $data['capParticipants'], $data['type']);
            $room->_host = $data['host'];
            $room->addParticipant($players[0]);
            $rooms[$rooms_max_id] = $room;
        }
        return $this->render('default/lobby.html.twig', ["room" => get_object_vars($room)]);
    }

    /**
     * @Route("/saloon", name="dcqtv_saloon")
     */
    public function saloonAction(Request $request)
    {
        return $this->render('default/saloon.html.twig');
    }

    /**
     * @Route("/questions", name="dcqtv_questions")
     */
    public function questionsAction(Request $request)
    {
        return $this->render('default/questions.html.twig');
    }
}
