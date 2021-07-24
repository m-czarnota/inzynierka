<?php

namespace App\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route (path="/arrange", name="app_game_arrange")
     */
    public function arrangeAction(): Response
    {
        return $this->render('game/arrange.html.twig');
    }
}