<?php

namespace App\Controller\Frontend;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Form\SearchArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use App\Search\SearchArticle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/articles', name: 'app.articles')]
class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $repo,
        private CommentaireRepository $repoComment
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(Request $request): Response|JsonResponse
    {
        $filter = new SearchArticle();

        $filter->setPage($request->query->get('page', 1));  // $request->query : on récupère ce qu'il y a dans le GET de la Request - par défaut on donne la valeur 1

        $form = $this->createForm(SearchArticleType::class, $filter);
        $form->handleRequest($request);

        $articles = $this->repo->findSearch($filter);
        // dump($filter);

        if($request->query->get('ajax')) {  // on regarde si on a le paramètre get ajax -> si oui, ça veut dire qu'on a envoyé une requete en ajax
            /**
             * On envoie la réponse en JSON avec le nouveau code HTML de chaque composant de la page
             */
            return new JsonResponse([
                'content' => $this->renderView('Components/_articleList.html.twig', [ // renderView renvoie la réponse en format chaine de caractères
                    'articles' => $articles,
                ]),
                'sorting' => $this->renderView('Components/_sorting.html.twig', [
                    'articles' => $articles,
                ]),
                'pagination' => $this->renderView('Components/_pagination.html.twig', [
                    'articles' => $articles,
                ]),
                'count' => $this->renderView('Components/_count.html.twig', [
                    'articles' => $articles,
                ]),
                'totalPage' => ceil($articles->getTotalItemCount() / $articles->getItemNumberPerPage()),
            ]);
        }

        return $this->render('Frontend/Article/index.html.twig', [
            'articles' => $articles,
            'form' => $form,
        ]);
    }

    #[Route('/details/{slug}', name: '.show', methods: ['GET', 'POST'])]
    public function show(?Article $article, Request $request): Response|RedirectResponse
    {
        if (!$article instanceof Article || !$article->isEnable()) {
            $this->addFlash('error', 'Article non trouvé');

            return $this->redirectToRoute('app.articles.index');
        }

        $commentaire = new Commentaire();

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire
                ->setUser($this->getUser())
                ->setEnable(true)
                ->setArticle($article);

            $this->repoComment->save($commentaire);

            $this->addFlash('success', 'Votre commentaire a bien été pris en compte');

            return $this->redirectToRoute('app.articles.show', [
                'slug' => $article->getSlug(),
            ], Response::HTTP_FOUND);
        }

        return $this->render('Frontend/Article/show.html.twig', [
            'article' => $article,
            'commentaires' => $this->repoComment->findActiveByArticle($article),
            'form' => $form
        ]);
    }
}
