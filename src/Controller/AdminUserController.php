<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminUserController extends AbstractController
{
    #[Route('/users', name: 'app_user_show')]
    public function showUsers(EntityManagerInterface $em): Response
    {

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/new', name: 'app_user_new')]
    public function createUser(Request $req, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher,): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'is_registration_form' => true,
            'is_admin_form_create' => true,
        ]);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $user = $form->getData();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'User created successfully!');
            return $this->redirectToRoute('app_user_show');
        }

        return $this->render('admin_user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws ORMException
     */
    #[Route('/users/edit/{id}', name: 'app_user_edit')]
    public function editUser(User $user, Request $req, EntityManagerInterface $em): Response
    {
        $em->refresh($user);
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'is_admin_form_edit' => true,
        ]);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User updated successfully!');
            return $this->redirectToRoute('app_user_show');
        }

        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/users/delete/{id}', name: 'app_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('app_user_show');
    }

}
