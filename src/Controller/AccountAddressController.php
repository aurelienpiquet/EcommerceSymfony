<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    /**
     * @Route("/mon-compte/mes-adresses", name="account_address")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, AddressRepository $addressRepository): Response
    {
        $addresses = $addressRepository->findBy(['user' => $this->getUser()]);
        return $this->render('account/addresses.html.twig', [
            'title' => "Vos adresses",
            'addresses' => $addresses,
        ]);
    }

    /**
     * @Route("/mon-compte/mes-adresses/ajouter", name="account_address_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $address->setSelected(false);
            $entityManager->persist($address);
            $entityManager->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_crud.html.twig', [
            'title' => "Vos adresses",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mon-compte/mes-adresses/mise-a-jour/{id}", name="account_address_update")
     */
    public function update(Request $request, EntityManagerInterface $entityManager, AddressRepository $addressRepository, $id): Response
    {
        $address = $addressRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        $form = $this->createForm(AddressType::class, $address);

        if ($address) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($address);
                $entityManager->flush();
                return $this->redirectToRoute('account_address');
            }
        }
        return $this->render('account/address_crud.html.twig', [
            'title' => "Vos adresses",
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/mon-compte/mes-adresses/supprimer/{id}", name="account_address_remove")
     */
    public function remove(AddressRepository $addressRepository, $id, EntityManagerInterface $entityManager): Response
    {
        $address = $addressRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if ($address) {
            $entityManager->remove($address);
            $entityManager->flush();
        }
        return $this->redirectToRoute('account_address');
    }
    /**
     * @Route("/mon-compte/mes-adresses/selectionner/{id}", name="account_address_select")
     */
    public function select(AddressRepository $addressRepository, $id, EntityManagerInterface $entityManager): Response
    {
        $address = $addressRepository->findOneBy(['id' => $id, 'user' => $this->getUser()]);
        if ($address) {
            $addresses = $addressRepository->findBy(['user' => $this->getUser()]);
            foreach($addresses as $addresse) {
                $addresse->setSelected(false);
            }
            $address->setSelected(true);
            $entityManager->persist($address);
            $entityManager->flush();
        }
        return $this->redirectToRoute('account_address');
    }






}
