<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\MailJet;
use App\Entity\Command;
use App\Repository\CommandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/ma-commande/create-session/{reference}", name="stripe_create_session", methods="POST")
     */
    public function index(Cart $cart, $reference, EntityManagerInterface $entityManager, CommandRepository $commandRepository): Response
    {

        Stripe::setApiKey('sk_test_51JbRxgIuZ3bNcJNfGUZF6lBjfcizl5YlIjynHSBUrzoDGl7w5Gc14LVmZVmYPyChqvqszXsOyZmIsgfwr9B660yr00wOCCOnxf');
        $articles_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $command = $commandRepository->findOneBy(['reference' => $reference]);

        if (!$command) {
            return $this->redirectToRoute('cart');
        }

        foreach($command->getOrderDetails()->getValues() as $order) {
            $articles_for_stripe[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => $order->getArticle()->getName(),
                        'images' => [$YOUR_DOMAIN . "/upload/article/" . $order->getArticle()->getIllustration()],
                    ],
                    'unit_amount' => $order->getArticle()->getPrice(),
                ],
                'quantity' => $order->getQuantity(),
            ];
        }

        $articles_for_stripe[] = [
            'price_data' => [
                'currency' => 'EUR',
                'product_data' => [
                    'name' => $command->getCarrierName(),
                    'images' => [],
                ],
                'unit_amount' => $command->getCarrierPrice(),
            ],
            'quantity' => 1,
        ];

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$articles_for_stripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . "/ma-commande/merci/{CHECKOUT_SESSION_ID}",
            'cancel_url' => $YOUR_DOMAIN . "/ma-commande/erreur/{CHECKOUT_SESSION_ID}",
        ]);

        $command->setStripeSessionId($checkout_session->id);
        $entityManager->persist($command);
        $entityManager->flush();
        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        dd('checkout');
    }

    /**
     * @Route("/ma-commande/merci/{stripeSessionId}", name="stripe_thanks_session")
     */
    public function thanks(Cart $cart, EntityManagerInterface $entityManager, CommandRepository $commandRepository, $stripeSessionId)
    {
        $command = $commandRepository->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$command || $command->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($command->getState() == 0) {
            $command->setState(1);
            $entityManager->persist($command);
            $entityManager->flush();

            foreach($command->getOrderDetails()->getValues() as $order) {
                $article = $order->getArticle();
                $article->setStock($article->getStock() - $order->getQuantity());
                $entityManager->persist($article);
            }
            $entityManager->flush();
            $cart->delete();
            $mail = new MailJet();
            $content = "Votre commande a bien été enregistrée. Vous pouvez suivre son status dans votre compte.";
            $mail->send($command->getUser()->getEmail(), $command->getUser()->getFirstname(), "Command $command", $content);
        }

        return $this->render('command/thanks.html.twig', [
            'title' => "Confirmation de votre commande.",
            'command' => $command,
        ]);
    }

    /**
     * @Route("/ma-commande/erreur/{stripeSessionId}", name="stripe_cancel_session")
     */
    public function cancel(EntityManagerInterface $entityManager, CommandRepository $commandRepository, $stripeSessionId)
    {
        $command = $commandRepository->findOneBy(['stripeSessionId' => $stripeSessionId]);

        if (!$command || $command->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('command/error404.html.twig', [
            'title' => "Erreur de paiement",
            'command' => $command,
        ]);
    }
}
