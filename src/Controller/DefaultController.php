<?php namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 *
 * @Route("/", name="app_")
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', []);
    }
}
