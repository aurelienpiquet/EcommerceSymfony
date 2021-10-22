<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\AddCartType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart, Request $request): Response
    {
        $cart_complete = $cart->cart_complete();

        return $this->render('cart/index.html.twig', [
            'title' => "Récapitulatif de votre panier",
            'cart' => $cart_complete,
            'total_price' => $cart->getTotalPrice(),
        ]);
    }

    /**
     * @Route("/mon-panier/ajouter/{id}", name="cart-add")
     */
    public function add($id, Cart $cart, Request $request, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(AddCartType::class, null);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $request->request->get('add_cart')['addNumber'];
            $article = $articleRepository->findOneBy(['id' => $id]);
            if ($article && $quantity > 0) {
                $stock = $article->getStock();
                if ($quantity >= $stock) {
                    $quantity = $stock;
                }
                $cart->add($id, $quantity);
            }
        }
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/ajouter-un/{id}", name="cart-add-one")
     */
    public function addOne($id, Cart $cart, Request $request, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['id' => $id]);
        if ($cart->get()[$id] < $article->getStock()) {
            $cart->add($id, 1);
        }
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/retirer-un/{id}", name="cart-remove-one")
     */
    public function removeOne($id, Cart $cart, ArticleRepository $articleRepository)
    {
        $cart->remove($id, 1);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/mon-panier/supprimer", name="cart-delete")
     */
    public function delete(Cart $cart): Response
    {
        $cart->delete();
        return $this->redirectToRoute('articles');
    }
}


