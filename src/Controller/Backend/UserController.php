<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users', name: 'admin.users')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $repo,
    ) {
    }

    #[Route('', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/User/index.html.twig', [
            'users' => $this->repo->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: '.update', methods: ['GET', 'POST'])]
    public function update(?User $user, Request $request): Response|RedirectResponse
    {
        if (!$user instanceof User) {
            $this->addFlash('error', 'Utilisateur non trouvé');

            return $this->redirectToRoute('admin.users.index');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($user);

            $this->addFlash('success', 'Utilisateur mis à jour avec succès');

            return $this->redirectToRoute('admin.users.index');
        }

        return $this->render('Backend/User/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: '.delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $user = $this->repo->find($request->request->get('id', 0));

        if (!$user instanceof User) {
            $this->addFlash('error', 'Utilisateur non trouvé');

            return $this->redirectToRoute('admin.users.index');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('token'))) {
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer un compte connecté actuellement');

                return $this->redirectToRoute('admin.users.index');
            }

            if (\count($user->getArticles()) > 0) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer un utilisateur qui a écrit un ou plusieurs articles');

                return $this->redirectToRoute('admin.users.index');
            }

            $this->repo->remove($user);

            $this->addFlash('success', 'Utilisateur supprimé avec succès');

            return $this->redirectToRoute('admin.users.index');
        }

        $this->addFlash('error', 'Token invalid');

        return $this->redirectToRoute('admin.users.index');
    }
}
