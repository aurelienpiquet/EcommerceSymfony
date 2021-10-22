<?php

namespace App\Controller;

use App\Form\ModifyPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    /**
     * @Route("/mon-compte/modifier-mot-de-passe", name="account_modify_password")
     */
    public function modify(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ModifyPasswordType::class, $user);
        $notification = "";
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old_password = $form->get('old_password')->getData();
            if ($hasher->isPasswordValid($user, $old_password)) {
                $new_password = $form->get('new_password')->getData();
                $user->setPassword($hasher->hashPassword($user, $new_password));
                $entityManager->persist($user);
                $entityManager->flush();
                $notification = "Votre mot de passe a bien été mise à jour.";

            }
            else {
                $notification = "Le mot de passe saisie n'est pas le bon.";
            }
        }
        return $this->render('account/password.html.twig', [
            'title' => "Modifier votre mot de passe",
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
