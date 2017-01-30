<?php

namespace AppBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Guzzle\Http\Message\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Room;
use AppBundle\Entity\Player;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        // Permet de restreindre l'accès. A voir si on laisse.
        /*$user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }*/

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
      
        
        // GET dans le cas où on veut rentrer dans un salon privé (ou public aussi au final) avec le code
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
                    return $this->render('default/lobby.html.twig', ["error" => ["message" => "Le salon n'existe pas ou plus."]]);
                }
            }
        }
      
        $tab = ["room" => get_object_vars($room),
        "player_role" => "participant",
        "player_id" => isset($new_player)
            ?$new_player->getId()
            :$player_host->getId(),
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
        $rooms = $repository->findAll();

        //var_dump($rooms);

        return $this->render('default/saloon.html.twig', ["rooms" => $rooms]);
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
    public function gameAction(Request $request){

        // Si on doit recharger la vue de quelqu'un déjà dans le jeu
        if($request->getMethod() == 'POST') {
            $data = $request->request->all();

            // On entre dans la phase de vote
            if(isset($data['game_stage_participant'])) {
                // TODO : Selon le type de joueur (participant ou audience), on utilise une vue différente
                // On entre dans la phase de jeu en tant que participant
                return $this->render('game/game_stage_participant.html.twig');

            } else if(isset($data['game_stage_audience'])){

                // On entre dans la phase de jeu en tant que jury
                return $this->render('game/game_stage_audience.html.twig');

            } else if(isset($data['vote_stage'])){

                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le pire cadeau d'anniversaire",
                        "proposition1" => empty($data['answer'])?'Aucune réponse donnée :(':$data['answer'], "proposition2" => "Du déodorant",
                        "participant1" => "Test", "participant2" => "Radium",
                        "next_post" => "vote_stage2"]);

            } else if(isset($data['vote_stage2'])){
                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le pire métier du monde",
                        "proposition1" => "Pousseur dans le métro", "proposition2" => "Homme politique",
                        "participant1" => "Lina", "participant2" => "HenryMichel",
                        "next_post" => "vote_stage3"]);

            } else if(isset($data['vote_stage3'])){
                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le nom de votre entreprise de vente de bateaux",
                        "proposition1" => "Ca m'boat", "proposition2" => "A voile et à vapeur",
                        "participant1" => "Chou", "participant2" => "Kevin",
                        "next_post" => "vote_stage4"]);

            } else if(isset($data['vote_stage4'])){
                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le secret d'un repas de Noël réussi",
                        "proposition1" => "Une superbe bûche de glace", "proposition2" => "L'anecdote bien grasse du grand-père",
                        "participant1" => "Clém", "participant2" => "Tatawa",
                        "next_post" => "leaderboard_stage"]);

            // On arrive au classement
            } else if(isset($data['leaderboard_stage'])){
                return $this->render('game/leaderboard_stage.html.twig', []);
            }

        }

    }

}
