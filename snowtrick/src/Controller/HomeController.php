<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class HomeController
 * @package App\Controller
 * @Route(name="home_")
 */
class HomeController extends AbstractController
{

    /**
     * Class HomeController
     * @Route("/home", name="index")
     */
    function home(Request $request, TrickRepository $trickRepository){
        $request->query->get("data");

        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }
}