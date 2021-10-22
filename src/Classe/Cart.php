<?php

namespace App\Classe;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $total_price;
    public $cart_complete;
    public $articleRepository;

    public function __construct(SessionInterface $session, ArticleRepository $articleRepository)
    {
        $this->session = $session;
        $this->total_price = 0;
        $this->cart_complete = [];
        $this->articleRepository = $articleRepository;
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function add($id, $quantity)
    {
        $cart = $this->get();

        if (!empty($cart[$id])) {
            $cart[$id] += $quantity;
        }
        else {
            $cart[$id] = $quantity;
        }
        $this->session->set('cart', $cart);
        $this->nb_articles();
    }

    public function remove(int $id, int $quantity_to_remove)
    {
        $cart = $this->get();

        if ($quantity_to_remove >= $cart[$id]) {
            unset($cart[$id]);
        }
        else {
            $cart[$id] = $cart[$id] - $quantity_to_remove;
        }
        $this->session->set('cart', $cart);
        $this->nb_articles();
    }

    public function delete()
    {
        $this->session->remove('cart');
        $this->nb_articles();
    }

    public function nb_articles()
    {
        $cart = $this->get();
        $sum = 0;
        if ($cart) {
            foreach($cart as $id => $quantity) {
                $sum += $quantity;
            }
        }
        $this->session->set('nb_articles', $sum);
        return $sum;
    }

    public function cart_complete()
    {
        $cart = $this->get();
        if ($cart) {
            foreach ($cart as $id => $quantity) {
                $article = $this->articleRepository->findOneBy(['id' => $id]);
                if ($article) {
                    $this->cart_complete[] = [
                        'article' => $article,
                        'quantity' => $quantity,
                    ];
                    $this->total_price += $article->getPrice() * $quantity;
                } else {
                    $this->remove($id, $quantity);
                }
            }
        }
        $this->nb_articles();
        return $this->cart_complete;
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

};