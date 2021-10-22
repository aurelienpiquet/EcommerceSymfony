<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountCommandController extends AbstractController
{
    /**
     * @Route("/mon-compte/mes-commandes", name="account_command")
     */
    public function index(CommandRepository $commandRepository): Response
    {
        $commands = $commandRepository->findPayedCommand($this->getUser());
        return $this->render('account/commands.html.twig', [
            'title' => "Vos commandes",
            'commands' => $commands,
        ]);
    }

    /**
     * @Route("/mon-compte/mes-commandes/{reference}", name="account_command_detail")
     */
    public function detail(CommandRepository $commandRepository, $reference): Response
    {
        $command = $commandRepository->findOneBy(['user' => $this->getUser(), 'reference' => $reference]);
        return $this->render('account/command_details.html.twig', [
            'title' => "Détail de la commande N°$reference",
            'command' => $command,
        ]);
    }
}
