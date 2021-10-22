<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Command;
use App\Entity\OrderDetail;
use App\Form\CommandType;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandController extends AbstractController
{
    /**
     * @Route("/ma-commande", name="command")
     */
    public function index(SessionInterface $session, Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }
        $cart->cart_complete();

        $form = $this->createForm(CommandType::class, null, [
            'user' => $this->getUser(),
        ]);

        return $this->render('command/index.html.twig', [
            'title' => 'Ma commande',
            'form' => $form->createView(),
            'cart' => $cart,
        ]);
    }
    /**
     * @Route("/ma-commande/récapitulatif", name="command-recap", methods={"POST"})
     */
    public function recap(SessionInterface $session, Cart $cart, Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('address-add');
        }
        $cart->cart_complete();
        $form = $this->createForm(CommandType::class, null,  [
            'user' => $this->getUser()
            ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sum = 0;
            $date = new DateTime();
            $command = new Command();
            $reference = $date->format('dmY').'-'.uniqid();
            $command->setReference($reference);
            $command->setUser($this->getUser());
            $command->setCreatedOn($date);
            $command->setCarrierName($form->get('carrier')->getData()->getName());
            $command->setCarrierPrice($form->get('carrier')->getData()->getPrice());
            $command->setState(0);
            $command->setDeliveryInformation($form->get('addresses')->getData()->__toString());

            $entityManager->persist($command);

            foreach($cart->cart_complete as $article) {
                $order_detail = new OrderDetail();
                $order_detail->setCommand($command);
                $order_detail->setArticle($article['article']);
                $order_detail->setQuantity($article['quantity']);
                $order_detail->setPrice($article['article']->getPrice());
                $order_detail->setTotal();
                $entityManager->persist($order_detail);
                $sum += $order_detail->getTotal();
            }

            $entityManager->flush();

            return $this->render('command/recap.html.twig', [
                'title' => "Récapitulatif de ma commande",
                'cart' => $cart,
                'carrier' => $command->getCarrierPrice(),
                'sum' => $sum,
                'command' => $command,
                'reference' => $command->getReference(),
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
