<?php

namespace AppBundle\Entity;

class Room
{
    protected $_id; // Int : L'id du salon, il est unique et permet de l'identifier
    public $_participants; // array<Player>
    public $_audience; // array<Player>
    public $_state; // Etat du salon / partie -> waiting_players/starting/waiting_participants/voting/leaderboard/end

    // Variables statiques à mettre dans en global plus tard
    public $_cap_participants = 8;
    public $_cap_audience = 1000;

    public function __construct($id){
        $this->_id = $id;
        $this->_participants = array();
        $this->_audience = array();
        $this->_state = waiting_players;
    }

    /**
    * Ajout d'un participant dans le salon
    */
    public function addParticipant(Player $player){
        if(count($this->_participants) < $this->_cap_participants){
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
        if(count($this->_audience) < $this->_cap_audience){
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
