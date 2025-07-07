<?php

/**
 * Home Controller.
 *
 * @author Konrad Stomski <konrad.stomski@student.uj.edu.pl>
 *
 * @copyright 2025 Konrad Stomski
 *
 * @license MIT
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling home page requests.
 */
class HomeController extends AbstractController
{
    /**
     * Display the home page.
     *
     * @return Response The rendered home page
     */
    #[\Symfony\Component\Routing\Attribute\Route(
        '/{_locale}',
        name: 'app_home',
        requirements: ['_locale' => 'en|pl'],
        defaults: ['_locale' => 'en']
    )]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
