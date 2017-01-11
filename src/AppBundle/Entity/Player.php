<?php

namespace AppBundle\Entity;

class Player
{
    protected $_id; // Int : L'id de l'utilisateur, il est unique et permet de l'identifier
    public $_pseudo; // String
    public $_state; // String : Où le joueur en est : home/menus/room_waiting/ingame...
    public $_room; // Room : Dans quelle room est le joueur

    public function __construct($id, $pseudo, $state)
    {
        $this->_id = $id;
        $this->_pseudo = $pseudo;
        $this->_state = $state;
        $this->_room = -1;
    }


}
