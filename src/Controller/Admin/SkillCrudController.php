<?php

namespace App\Controller\Admin;

use App\Entity\Skill;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SkillCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Skill::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Skill')
            ->setEntityLabelInPlural('Skill')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit %entity_name%')
            ->setSearchFields(['id', 'skill']);
    }

    public function configureFields(string $pageName): iterable
    {
        $skill = TextField::new('skill');
        $enabled = Field::new('enabled');
        $id = IntegerField::new('id', 'ID');
        $opportunities = AssociationField::new('opportunities');
        $volunteers = AssociationField::new('volunteers');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$skill, $enabled];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $skill, $enabled, $opportunities, $volunteers];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$skill, $enabled];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$skill, $enabled];
        }
    }
}
