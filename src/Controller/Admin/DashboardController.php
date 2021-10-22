<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Carrier;
use App\Entity\Categorie;
use App\Entity\Command;
use App\Entity\Contact;
use App\Entity\Creator;
use App\Entity\NewCollection;
use App\Entity\OrderDetail;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(CommandCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ECommerce Symfony');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Contact', "fas fa-envelope-open-text", Contact::class);
        yield MenuItem::linkToCrud('Créateur', 'fas fa-user-tie', Creator::class);
        yield MenuItem::linkToCrud('Nouvelle Collection', 'fas fa-tshirt', NewCollection::class);
        yield MenuItem::linkToCrud('Article', 'fas fa-socks', Article::class);
        yield MenuItem::linkToCrud('Categorie', 'fas fa-list', Categorie::class);
        yield MenuItem::linkToCrud('Transporteur', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Commande', 'fas fa-shopping-cart', Command::class);
        yield MenuItem::linkToCrud('Commande Détails', 'fas fa-receipt', OrderDetail::class);
    }
}
