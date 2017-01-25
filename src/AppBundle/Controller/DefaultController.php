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
        $em = $this->getDoctrine()->getManager();

        // TODO : Creer une session invité à l'utilisateur s'il n'est pas connecté, récupérer ses infos sinon
        $player_host = new Player('test', "room_waiting");
        $em->persist($player_host);
        $em->flush();

        if($request->getMethod() == 'POST'){
            $data = $request->request->all();

            //var_dump($data);

            /*
             * Si on accède à cette page en créant le salon
             * Dans $data, on a 'capParticipants', 'type' et 'host'
             */
            if(isset($data['lobby_creation'])) {
                $room = new Room($data['capParticipants'], $data['type']);
                $room->host = $player_host;
                $room->addParticipant($player_host);

                // tells Doctrine you want to (eventually) save the Room (no queries yet)
                $em->persist($room);

                // actually executes the queries (i.e. the INSERT query)
                $em->flush();
            }

            /*
             * Si on accède à cette page en rejoignant le salon
             * Dans $data, on a 'player_role', 'player_pseudo'
             */
            if(isset($data['lobby_join'])){
                $repository = $this->getDoctrine()->getRepository('AppBundle:Room');
                $room = $repository->find($data['lobby_id']);
                $new_player = new Player($data['player_pseudo'], 'room_waiting');

                $em->persist($new_player);
                $em->flush();

                if($data['lobby_player_role'] == "participant"){
                    $room->addParticipant($new_player);
                }else if($data['lobby_player_role'] == "jury"){
                    $room->addAudience($new_player);
                }

                // tells Doctrine you want to (eventually) save the Room (no queries yet)
                $em->persist($room);

                // actually executes the queries (i.e. the INSERT query)
                $em->flush();
            }
        }
      
        
        // GET dans le cas où on veut rentrer dans un salon privé avec le code
        if($request->getMethod() == 'GET'){
            $data = $request->query->all();

            if(isset($data['lobby_code'])){
                $repository = $this->getDoctrine()->getRepository('AppBundle:Room');
                $room = $repository->findOneBy(array("type" => $data['lobby_code']));

                // On a trouvé le salon dans lequel il veut rentrer
                if($room != null){

                    $room->addParticipant($player_host);

                    // tells Doctrine you want to (eventually) save the Room (no queries yet)
                    $em->persist($room);

                    // actually executes the queries (i.e. the INSERT query)
                    $em->flush();

                // Code d'un salon qui n'existe pas ou plus
                } else {
                    return $this->render('default/lobby.html.twig', ["error" => ["message" => "Salon introuvable"]]);
                }
            }
        }
      
        $tab = ["room" => get_object_vars($room),
        "player_role" => "participant",
        "player_pseudo" => isset($new_player)
            ?$new_player->getPseudo()
            :$player_host->getPseudo()];
      
        if(isset($data['lobby_join'])){
            $tab["joining"] = true;
        } else if(isset($data['lobby_creation'])) {
            $tab["creation"] = true;
        }

        return $this->render('default/lobby.html.twig', $tab);
    }

    /**
     * @Route("/saloon", name="dcqtv_saloon")
     */
    public function saloonAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Room');
        $rooms = $repository->find(1);

        var_dump($rooms);

        return $this->render('default/saloon.html.twig');
    }

    /**
     * @Route("/questions", name="dcqtv_questions")
     */
    public function questionsAction(Request $request)
    {
        return $this->render('default/questions.html.twig');
    }

    /**
     * @Route("/game", name="dcqtv_game")
     */
    public function gameStageParticipantAction(Request $request){
        return $this->render('game/game_stage_participant.html.twig');
    }
}
