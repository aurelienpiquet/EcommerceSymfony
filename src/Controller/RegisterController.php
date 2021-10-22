<?php

namespace App\Controller;

use App\Classe\MailJet;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/créer-un-compte", name="register")
     */
    public function index(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $check = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if (!$check) {
                $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
                $entityManager->persist($user);
                $entityManager->flush();

                $mail = new MailJet();
                $mail->send($user->getEmail(), $user->getFirstname(), "Confirmation de création de compte", "Votre compte a
            bien été crée");
            }
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'title' => 'Créer un compte',
            'form' => $form->createView(),

        ]);
    }
}
