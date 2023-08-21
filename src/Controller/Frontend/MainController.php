<?php

namespace App\Controller\Frontend;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('', name: 'app.homepage', methods: ['GET'])]
    public function index(ArticleRepository $articleRepo): Response
    {
        return $this->render('Frontend/Home/index.html.twig', [
            'articles' => $articleRepo->findLatest(3),
        ]);
    }
}
