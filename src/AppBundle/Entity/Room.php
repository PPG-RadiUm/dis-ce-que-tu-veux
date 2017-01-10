<?php

namespace AppBundle\Entity;

class Room
{
    protected $_id; // Int : L'id du salon, il est unique et permet de l'identifier
    public $_participants; // array<Player>
    public $_audience; // array<Player>
    public $_state; // Etat du salon / partie -> waiting_players/starting/waiting_participants/voting/leaderboard/end
    public $_capParticipants;
    public $_type; // Si le salon est public (visible sur la liste des salons) ou privé (accessible uniquement via lien)

    // Variables statiques à mettre dans en global plus tard
    public $_capAudience = 1000;

    public function __construct($id, $capParticipants, $type){
        $this->_id = $id;
        $this->_participants = array();
        $this->_audience = array();
        $this->_state = waiting_players;
        $this->_capParticipants = $capParticipants;
        $this->_type = $type;
    }

    /**
    * Ajout d'un participant dans le salon
    */
    public function addParticipant(Player $player){
        if(count($this->_participants) < $this->_capParticipants){
            $player->$room = $this->_id;
            array_push($this->_participants);
            return true;
        } else {
            return false;
        }
    }

    /**
    * Suppression d'un participant dans le salon
    */
    public function removeParticipant(Player $player){
        $player->$room = null;
        unset($this->_participants[$player]);
    }

    /**
    * Ajout d'un membre de l'audience dans le salon
    */
    public function addAudience(Player $player){
        if(count($this->_audience) < $this->_capAudience){
            $player->$room = $this->_id;
            array_push($this->_audience);
            return true;
        } else {
            return false;
        }
    }

    /**
    * Suppression d'un membre de l'audience dans le salon
    */
    public function removeAudience(Player $player){
        $player->$room = null;
        unset($this->_audience[$player]);
    }

    /**
    * Démarrage de la partie
    */
    public function start(){
        $this->_state = starting;

        // On va choisir des propositions aléatoires dans la BDD et associer les participants deux à deux devant une proposition à chaque fois
    }
}
