<?php

namespace App\Controller\Admin;

use App\Entity\Representative;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RepresentativeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Representative::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
                        ->setPageTitle(Crud::PAGE_EDIT, 'Edit %entity_name%')
                        ->setHelp('index', 'Locking staff deactivates nonprofit and blocks staff log in. Replacing also removes current staff.')
                        ->setSearchFields(['id', 'roles', 'email', 'fname', 'sname', 'confirmationToken', 'replacementStatus']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                        ->disable('new', 'edit', 'delete');
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ArrayField::new('roles');
        $password = TextField::new('password');
        $email = TextField::new('email');
        $fname = TextField::new('fname');
        $sname = TextField::new('sname');
        $lastLogin = DateTimeField::new('lastLogin');
        $confirmationToken = TextField::new('confirmationToken');
        $tokenExpiresAt = DateTimeField::new('tokenExpiresAt');
        $locked = BooleanField::new('locked');
        $enabled = BooleanField::new('enabled');
        $replacementStatus = TextField::new('replacementStatus');
        $initiated = DateField::new('initiated');
        $completed = DateField::new('completed');
        $nonprofit = AssociationField::new('nonprofit');
        $id = IntegerField::new('id', 'ID');
        $replace = TextareaField::new('replace')->setTemplatePath('Admin/replace_staff.html.twig');
        $fullName = TextareaField::new('fullName');
        $orgname = TextareaField::new('orgname');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$replace, $fullName, $email, $orgname];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $replacementStatus, $initiated, $completed, $nonprofit];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $replacementStatus, $initiated, $completed, $nonprofit];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $replacementStatus, $initiated, $completed, $nonprofit];
        }
    }
}
