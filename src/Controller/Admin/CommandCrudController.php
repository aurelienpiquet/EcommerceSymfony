<?php

namespace App\Controller\Admin;

use App\Classe\MailJet;
use App\Entity\Command;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use SebastianBergmann\CodeCoverage\Report\Text;

class CommandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Command::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updateCommand = Action::new('updateCommand', 'Préparation en cours',"fas fa-socks")->linkToCrudAction('updateCommand');
        $sentCommand = Action::new('sentCommand', "Commande Envoyée", "fas fa-truck")->linkToCrudAction('sentCommand');
        return $actions
            ->add('detail', $sentCommand)
            ->add('detail', $updateCommand)
            ->add('index', 'detail');
    }

    public function updateCommand(AdminContext $adminContext, EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $command = $adminContext->getEntity()->getInstance();
        if ($command->getState() == 1) {
            $command->setState(2);
            $entityManager->persist($command);
            $entityManager->flush();
            $this->addFlash('notice', "Modification effectuée");
        }

        $routeBuilder = $this->get(AdminUrlGenerator::class);
        $mail = new MailJet();
        $to_email = $command->getUser()->getEmail();
        $content = "";
        $subject = "Votre commande $command est en cours de préparation.";
        $mail->send($to_email, $command->getUser()->getFirstname(), $subject, $content);
        return $this->redirect($routeBuilder->setController(CommandCrudController::class)->setAction('index')->generateUrl());
    }

    public function sentCommand(AdminContext $adminContext, EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $command = $adminContext->getEntity()->getInstance();
        if ($command->getState() == 2) {
            $command->setState(3);
            $entityManager->persist($command);
            $entityManager->flush();
            $this->addFlash('notice', "Modification effectuée");
        }
        $mail = new MailJet();
        $to_email = $command->getUser()->getEmail();
        $content = "";
        $subject = "Votre commande $command a été envoyée.";
        $mail->send($to_email, $command->getUser()->getFirstname(), $subject, $content);
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(CommandCrudController::class)->setAction('index')->generateUrl());
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=> 'DESC']);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('reference', "Référence de Commande"),
            AssociationField::new('user', 'Acheteur'),
            DateField::new('createdOn', 'Passé le:')->setFormat('dd M Y')->renderAsNativeWidget(),
            TextField::new('carrierName', "Transporteur"),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('deliveryInformation')->hideOnIndex(),
            ChoiceField::new('state', "Status")->setChoices([
                'Commande non Payée' => 0,
                'Commande Payée' => 1,
                'Commande en préparation' => 2,
                'Commande Envoyée' => 3,
            ]),
        ];
    }

}
