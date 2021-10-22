<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class ArticleCrudController extends AbstractCrudController
{

    private $lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus commodo turpis fermentum justo ullamcorper lacinia. Mauris vestibulum ut lacus vel ullamcorper. Fusce luctus mauris eget tincidunt volutpat. Nulla congue, velit in ullamcorper bibendum, turpis nisl auctor metus, sed dapibus nibh arcu et ante. Sed eget libero mauris. Cras vitae risus cursus, condimentum massa a, scelerisque turpis. Vivamus neque tortor, blandit sed risus eget, congue porttitor lectus.";

    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            SlugField::new('slug')->setTargetFieldName('name'),
            TextField::new('subtitle'),
            TextareaField::new('description')->hideOnIndex(),
            ImageField::new('illustration')
                ->setBasePath('upload/article/')
                ->setUploadDir('public/upload/article/')
                ->setUploadedFileNamePattern('[randomhash].[extension]'),
            DateField::new('createdOn')->setFormat('Y-MM-dd')->renderAsNativeWidget(),
            AssociationField::new('creator'),
            AssociationField::new('newCollection'),
            AssociationField::new('categorie'),
            IntegerField::new('stock'),
            MoneyField::new('price')->setCurrency('EUR'),


        ];
    }

}
