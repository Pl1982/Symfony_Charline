<?php

namespace App\Controller\Backend;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commentaires', name: 'admin.commentaires')]
class CommentaireController extends AbstractController
{
    public function __construct(
        private CommentaireRepository $repo,
    ) {
    }

    #[Route('/{id}/comments', name: '.index')]
    public function index(?Article $article): Response|RedirectResponse
    {
        if (!$article instanceof Article) {
            $this->addFlash('error', 'Article introuvable');

            return $this->redirectToRoute('admin.articles.index');
        }

        return $this->render('Backend/Commentaires/index.html.twig', [
            'commentaires' => $article->getCommentaires(),
            'article' => $article,
        ]);
    }

    #[Route('/delete', name: '.delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $commentaire = $this->repo->find($request->request->get('id', 0));

        if (!$commentaire instanceof Commentaire) {
            $this->addFlash('error', 'Commentaire introuvable');

            return $this->redirectToRoute('admin.articles.index');
        }

        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('token'))) {
            $this->repo->remove($commentaire);
            $this->addFlash('success', 'Commentaire supprimé avec succès');

            return $this->redirectToRoute('admin.commentaires.index', [
                'id' => $commentaire->getArticle()->getId(),
            ]);
        }

        $this->addFlash('error', 'Token invalid');

        return $this->redirectToRoute('admin.commentaires.index', [
            'id' => $commentaire->getArticle()->getId(),
        ]);
    }

    #[Route('/switch/{id}', name: '.switch', methods: ['GET'])]
    public function switch(?Commentaire $commentaire): JsonResponse
    {
        if (!$commentaire instanceof Commentaire) {
            return $this->json([
                'status' => 'error',
                'message' => 'Commentaire introuvable',
            ]);
        }

        $commentaire->setEnable(!$commentaire->isEnable());

        $this->repo->save($commentaire);

        return $this->json([
            'status' => 'success',
            'message' => 'Commentaire mis à jour avec succès',
            'enable' => $commentaire->isEnable(),
        ]);
    }
}
