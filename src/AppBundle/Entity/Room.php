<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="room")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoomRepository")
 */
class Room
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * One Room has One Player (as host).
     * @ORM\OneToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id")
     */
    public $host; // host

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="room")
     */
    public $participants; // array<Player>

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="room2")
     */
    public $audience; // array<Player>

    /**
     * @ORM\Column(type="string")
     */
    public $state; // Etat du salon / partie -> waiting_players/starting/waiting_participants/voting/leaderboard/end

    /**
     * @ORM\Column(type="integer")
     */
    public $capParticipants;

    /**
     * @ORM\Column(type="string")
     */
    public $type; // Si le salon est public (visible sur la liste des salons), cette variable == 0, sinon s'il est privé (accessible uniquement via lien), cette variable == le code du salon pour y entrer



    // Variables statiques à mettre dans en global plus tard
    public $capAudience = 1000;

    public function __construct($capParticipants, $type)
    {
        $this->participants = array();
        $this->audience = array();
        $this->state = "waiting_players";
        $this->capParticipants = $capParticipants;
        if($type == 0){
            $this->type = $type;
        // Si le salon est privé, on lui génère un code pour y entrer dans $type
        } else {
            $this->type = $this->generateRoomCode();
        }
    }

    /**
     * Ajout d'un participant dans le salon
     */
    public function addParticipant(Player $player){

        if(is_array($this->participants) && (empty($this->participants) || count($this->participants) < $this->capParticipants)) {

            $player->room = $this;
            array_push($this->participants, $player);
            return true;

        } else if(is_object($this->participants)){

            $player->room = $this;
            $this->participants->add($player);
            return true;

        } else {
            return false;
        }
    }

    /**
     * Suppression d'un participant dans le salon
     */
    public function removeParticipant(Player $player){
        $player->room = null;
        unset($this->participants[$player]);
    }

    /**
     * Vérifie si un joueur est déjà présent dans la liste des participants
     */
    public function checkIsParticipant(Player $player)
    {
        return in_array($player, $this->participants);
    }

    /**
     * Ajout d'un membre de l'audience dans le salon
     */
    public function addAudience(Player $player){
        if(count($this->audience) < $this->capAudience){
            $player->room2 = $this;
            $this->audience->add($player);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Suppression d'un membre de l'audience dans le salon
     */
    public function removeAudience(Player $player){
        $player->room2 = null;
        unset($this->audience[$player]);
    }

    /**
     * Vérifie si un joueur est déjà présent dans la liste des participants
     */
    public function checkIsAudience(Player $player)
    {
        return in_array($player, $this->audience);
    }

    public function generateRoomCode(){
        $html = file_get_contents("https://www.random.org/strings/?num=1&len=4&digits=on&upperalpha=on&unique=on&format=html&rnd=id.".(time()+rand(0,100)));

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXpath($doc);

        $node = $xpath->query('//div[@id="invisible"]/pre[@class="data"]')->item(0);

        return trim($node->textContent);
    }

    /**
     * Démarrage de la partie
     */
    public function start(){
        $this->state = "starting";

        // On va choisir des propositions aléatoires dans la BDD et associer les participants deux à deux devant une proposition à chaque fois
    }
}

