<?php

namespace App\Controller\Admin;

use App\Entity\Volunteer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VolunteerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Volunteer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Volunteer')
            ->setEntityLabelInPlural('Volunteer')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit %entity_name%')
            ->setSearchFields(['id', 'roles', 'email', 'fname', 'sname', 'confirmationToken']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new', 'edit');
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
        $locked = BooleanField::new('locked')->setTemplatePath('Admin/locked_badge.html.twig');
        $enabled = BooleanField::new('enabled');
        $receiveEmail = BooleanField::new('receiveEmail');
        $focuses = AssociationField::new('focuses');
        $skills = AssociationField::new('skills');
        $id = IntegerField::new('id', 'ID');
        $fullName = TextareaField::new('fullName');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$locked, $enabled, $fullName, $email, $receiveEmail];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $receiveEmail, $focuses, $skills];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $receiveEmail, $focuses, $skills];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $receiveEmail, $focuses, $skills];
        }
    }
}
