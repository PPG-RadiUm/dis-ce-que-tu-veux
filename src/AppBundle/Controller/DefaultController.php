<?php

namespace AppBundle\Controller;

use Guzzle\Http\Message\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->render('default/lobby_configuration.html.twig');
    }

    /**
     * @Route("/lobby", name="dcqtv_lobby")
     */
    public function lobbyAction(Request $request)
    {
        return $this->render('default/lobby.html.twig');
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
