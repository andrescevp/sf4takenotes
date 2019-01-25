<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(NoteRepository $noteRepository): Response
    {
        return $this->render('default/homepage.html.twig', [
            'notes' => $noteRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }
}
