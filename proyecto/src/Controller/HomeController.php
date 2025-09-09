<?php

namespace App\Controller;

use App\Repository\MemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(MemeRepository $memeRepository): Response
    {
        $memes = $memeRepository->findBy(['isApproved' => true], ['id' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'memes' => $memes,
        ]);
    }
}