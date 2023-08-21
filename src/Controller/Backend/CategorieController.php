<?php

namespace App\Controller\Backend;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/categories', name: 'admin.categories')]
class CategorieController extends AbstractController
{
    public function __construct(
        private CategorieRepository $repo,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/Categorie/index.html.twig', [
            'categories' => $this->repo->findAll(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $categorie = new Categorie();

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($categorie);

            $this->addFlash('success', 'Catégorie créé avec succès');

            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('Backend/Categorie/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(?Categorie $categorie, Request $request): Response|RedirectResponse
    {
        if (!$categorie instanceof Categorie) {
            $this->addFlash('error', 'Catégorie non trouvée');

            return $this->redirectToRoute('admin.categories.index');
        }

        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($categorie);

            $this->addFlash('success', 'Catégorie mise à jour avec succès');

            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('Backend/Categorie/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: '.delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $categorie = $this->repo->find($request->get('id', 0));

        if (!$categorie instanceof Categorie) {
            $this->addFlash('error', 'Catégorie non trouvée');

            return $this->redirectToRoute('admin.categories.index');
        }

        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->get('token'))) {
            $this->repo->remove($categorie);

            $this->addFlash('success', 'Catégorie supprimée avec succès');

            return $this->redirectToRoute('admin.categories.index');
        }

        $this->addFlash('error', 'Token invalide');

        return $this->redirectToRoute('admin.categories.index');
    }

    #[Route('/switch/{id}', name: '.switch', methods: ['GET'])]
    public function switch(?Categorie $categorie): JsonResponse
    {
        if (!$categorie instanceof Categorie) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Catégorie non trouvée',
            ], 404);
        }

        $categorie->setEnable(!$categorie->isEnable());

        $this->repo->save($categorie);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Catégorie mise à jour avec succès',
            'enable' => $categorie->isEnable(),
        ], 201);
    }
}
