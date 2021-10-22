<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\CategorieRepository;
use App\Repository\NewCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(NewCollectionRepository $newCollectionRepository, CategorieRepository $categorieRepository): Response
    {
        $pages = [0,1,2,3];
        $collections = $newCollectionRepository->findAll();
        $categories = $categorieRepository->findAll();
        return $this->render('home/index.html.twig', [
            'title' => 'Acceuil',
            'collections' => $collections,
            'pages' => $pages,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/a-propos", name="about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'title' => 'Qui sommes-nous?',
        ]);
    }

    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function contact(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', "Votre message a bien été envoyé.");
            return $this->redirectToRoute('contact');
        }

        return $this->render('home/contact.html.twig', [
            'title' => 'Nous contacter',
            'form' => $form->createView(),
        ]);
    }

}
