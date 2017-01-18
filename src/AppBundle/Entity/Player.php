<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlayerRepository")
 */
class Player
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id; // Int : L'id de l'utilisateur, il est unique et permet de l'identifier

    /**
     * @ORM\Column(type="string")
     */
    protected $pseudo; // String

    /**
     * @ORM\Column(type="string")
     */
    public $state; // String : Où le joueur en est : home/menus/room_waiting/ingame...

    /**
     * Many Players have One Room.
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="participants")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     *
     */
    public $room; // Room : Dans quelle room est le joueur

    /**
     * Many Players have One Room.
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="audience")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     *
     */
    public $room2; // Room : Dans quelle room est le joueur

    public function __construct($pseudo, $state)
    {
        $this->pseudo = $pseudo;
        $this->state = $state;
        $this->room = null;
    }

    public function getId(){
        return $this->id;
    }

    public function getPseudo(){
        return $this->pseudo;
    }

}

