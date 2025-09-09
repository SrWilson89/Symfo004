<?php

namespace App\Controller;

use App\Entity\Meme;
use App\Form\MemeType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class MemeController extends AbstractController
{
    #[Route('/meme/new', name: 'app_meme_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $meme = new Meme();
        $form = $this->createForm(MemeType::class, $meme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagenFile = $form->get('imagen')->getData();

            if ($imagenFile) {
                try {
                    $fileName = $fileUploader->upload($imagenFile);
                } catch (FileException $e) {
                    // Manejar la excepción si algo falla al subir el archivo
                    $this->addFlash('error', 'Hubo un error al subir la imagen.');
                    return $this->redirectToRoute('app_meme_new');
                }
                $meme->setRutaImagen($fileName);
            }

            // Establece isApproved a false por defecto
            $meme->setIsApproved(false);

            $entityManager->persist($meme);
            $entityManager->flush();

            $this->addFlash('success', '¡Meme subido con éxito! Esperando aprobación de un moderador.');

            return $this->redirectToRoute('app_home'); // Asume que existe la ruta `app_home`
        }

        return $this->render('meme/new.html.twig', [
            'form' => $form,
        ]);
    }
}