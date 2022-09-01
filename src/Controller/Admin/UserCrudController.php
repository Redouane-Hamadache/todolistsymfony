<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDateFormat('...')
            // ...
            ->setPageTitle('index', 'To do list - Administration des utilisateurs')
            ->setPaginatorPageSize(10)
        ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('email')
                ->hideOnForm()
                ->setFormTypeOption('disabled','disabled'),
            TextField::new('username'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            ArrayField::new('roles'),
            TextField::new('googleId')
                ->hideOnForm(),
            TextField::new('githubId')
                ->hideOnForm(),

        ];
    }

}
