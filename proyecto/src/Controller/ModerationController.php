<?php

namespace App\Controller;

use App\Entity\Meme;
use App\Repository\MemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModerationController extends AbstractController
{
    #[Route('/moderacion', name: 'app_moderation')]
    public function index(MemeRepository $memeRepository): Response
    {
        // Solo mostramos los memes que NO han sido aprobados
        $memes = $memeRepository->findBy(['isApproved' => false]);

        return $this->render('moderation/index.html.twig', [
            'memes' => $memes,
        ]);
    }

    #[Route('/moderacion/approve/{id}', name: 'app_moderation_approve')]
    public function approve(Meme $meme, EntityManagerInterface $entityManager): Response
    {
        // Cambiamos el estado de isApproved a true
        $meme->setIsApproved(true);
        $entityManager->flush();

        $this->addFlash('success', '¡Meme aprobado con éxito!');

        return $this->redirectToRoute('app_moderation');
    }

    #[Route('/moderacion/reject/{id}', name: 'app_moderation_reject')]
    public function reject(Meme $meme, EntityManagerInterface $entityManager): Response
    {
        // Eliminamos el meme de la base de datos
        $entityManager->remove($meme);
        $entityManager->flush();

        $this->addFlash('success', '¡Meme rechazado y eliminado!');

        return $this->redirectToRoute('app_moderation');
    }
}