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
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        return $this->render('default/lobby_configuration.html.twig',
            ["host" => $user->getId(), "host_username" => $user->getUsername()]);
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
        $data = $request->request->all();

        $roomRepository = $this->getDoctrine()->getRepository('AppBundle:Room');

        $em = $this->getDoctrine()->getManager();

        $doFlush = false;

        // TODO : Creer une session invité à l'utilisateur s'il n'est pas connecté, récupérer ses infos sinon
        $playerRepository = $this->getDoctrine()->getRepository('AppBundle:Player');
        $player_host = $playerRepository->findOneByPseudo($data['player_pseudo']);
        // $player_host == null signifie que ce n'est pas un player existant. On le créé donc
        if($player_host == null){
            $player_host = new Player($data['player_pseudo'], "room_waiting");
            $em->persist($player_host);
            $em->flush();
        }

        if($request->getMethod() == 'POST'){
            //var_dump($data);

            /*
             * Si on accède à cette page en créant le salon
             * Dans $data, on a 'capParticipants', 'type' et 'host'
             */
            if(isset($data['lobby_creation'])) {
                $room = $roomRepository->findOneByHost($data['host']);
                if($room == null){
                    $room = new Room($data['capParticipants'], $data['type']);
                    $room->host = $player_host;
                    $room->addParticipant($player_host);
                    $doFlush = true;
                }
            }

            /*
             * Si on accède à cette page en rejoignant le salon
             * Dans $data, on a 'player_role', 'player_pseudo'
             */
            if(isset($data['lobby_join'])){
                $room = $roomRepository->find($data['lobby_id']);
                if(!$room->checkIsParticipant($player_host)){
                //if(!$room->checkIsParticipant($player_host) && !$room->checkIsAudience($player_host)){
                    if($data['lobby_player_role'] == "participant"){
                        $room->addParticipant($player_host);
                    }else if($data['lobby_player_role'] == "jury"){
                        $room->addAudience($player_host);
                    }
                }
                $doFlush = true;
            }
        }
      
        
        // GET dans le cas où on veut rentrer dans un salon privé (ou public aussi au final) avec le code
        if($request->getMethod() == 'GET'){

            if(isset($data['lobby_code'])){
                $room = $roomRepository->findOneBy(array("type" => $data['lobby_code']));

                // On a trouvé le salon dans lequel il veut rentrer
                if($room != null){
                    if(!$room->checkIsParticipant($player_host) && !$room->checkIsAudience($player_host)){
                        /*
                         * TODO
                         * Permettre de rejoindre une room privée en participant ou en jury ou
                         * ajouter les personnes qui rentrent en participant et quand le cap est atteint
                         * mettre les nouveaux en jury
                         */
                        if($data['lobby_player_role'] == "participant"){
                            $room->addParticipant($player_host);
                        }else if($data['lobby_player_role'] == "jury"){
                            $room->addAudience($player_host);
                        }
                        $doFlush = true;
                    }

                // Code d'un salon qui n'existe pas ou plus
                } else {
                    return $this->render('default/lobby.html.twig', ["error" => ["message" => "Le salon n'existe pas ou plus."]]);
                }
            }
        }

        if($doFlush){
            // tells Doctrine you want to (eventually) save the Room (no queries yet)
            $em->persist($room);

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();
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
        // Permet de restreindre l'accès.
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

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
                $dataScores = [
                    "Test",
                    "RadiUm",
                    "Lina",
                    "Henry Michel",
                    "Shou",
                    "Kevin",
                    "Clem",
                    "Tatawa"
                ];

                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le pire cadeau d'anniversaire",
                        "proposition1" => empty($data['answer'])?'Du papier cadeau':$data['answer'], "proposition2" => "Du déodorant",
                        "participant1" => "Test", "participant2" => "RadiUm",
                        "next_post" => "vote_stage2",
                        "scores" => $dataScores]);

            } else if(isset($data['vote_stage2'])){
                var_dump($data);
                $dataScores = json_decode((isset($data['scores_1'])) ? $data['scores_1'] : $data['scores_2']);

                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le pire métier du monde",
                        "proposition1" => "Pousseur dans le métro", "proposition2" => "Homme politique",
                        "participant1" => "Lina", "participant2" => "Henry Michel",
                        "next_post" => "vote_stage3",
                        "scores" => $dataScores]);

            } else if(isset($data['vote_stage3'])){
                $dataScores = json_decode((isset($data['scores_1'])) ? $data['scores_1'] : $data['scores_2']);

                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le nom de votre entreprise de vente de bateaux",
                        "proposition1" => "Ca m'boat", "proposition2" => "A voile et à vapeur",
                        "participant1" => "Shou", "participant2" => "Kevin",
                        "next_post" => "vote_stage4",
                        "scores" => $dataScores]);

            } else if(isset($data['vote_stage4'])){
                $dataScores = json_decode((isset($data['scores_1'])) ? $data['scores_1'] : $data['scores_2']);

                return $this->render('game/vote_stage.html.twig',
                    ["question" => "Le secret d'un repas de Noël réussi",
                        "proposition1" => "Une superbe bûche de glace", "proposition2" => "L'anecdote bien grasse du grand-père",
                        "participant1" => "Clem", "participant2" => "Tatawa",
                        "next_post" => "leaderboard_stage",
                        "scores" => $dataScores]);

            // On arrive au classement
            } else if(isset($data['leaderboard_stage'])){
                $dataScores = json_decode((isset($data['scores_1'])) ? $data['scores_1'] : $data['scores_2']);

                return $this->render('game/leaderboard_stage.html.twig', ["scores" => $dataScores]);
            }

        }

    }

}
