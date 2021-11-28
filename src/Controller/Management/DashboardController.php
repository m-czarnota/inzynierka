<?php

namespace App\Controller\Management;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route (path="/", name="app_dashboard_")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route (name="index")
     */
    public function indexAction(): Response
    {
        if (!$this->getUser()) {
            return $this->render('homepage.html.twig');
        }

        return $this->render('management/dashboard.html.twig');
    }
}