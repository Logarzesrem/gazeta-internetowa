<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    #[\Symfony\Component\Routing\Attribute\Route('/{_locale}', name: 'app_home', requirements: ['_locale' => 'en|pl'], defaults: ['_locale' => 'en'])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
