<?php

namespace App\Controller\Admin;

use App\Entity\Focus;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FocusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Focus::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Focus')
            ->setEntityLabelInPlural('Focus')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit %entity_name%')
            ->setSearchFields(['id', 'focus']);
    }

    public function configureFields(string $pageName): iterable
    {
        $focus = TextField::new('focus');
        $enabled = Field::new('enabled');
        $id = IntegerField::new('id', 'ID');
        $nonprofits = AssociationField::new('nonprofits');
        $volunteers = AssociationField::new('volunteers');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$focus, $enabled];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $focus, $enabled, $nonprofits, $volunteers];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$focus, $enabled];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$focus, $enabled];
        }
    }
}
