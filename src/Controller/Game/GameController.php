<?php

namespace App\Controller\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route (path="/game/{optional}", defaults={"optional"=""}, name="app_game")
     */
    public function arrangeAction(): Response
    {
        return $this->render('game/base.html.twig');
    }
}