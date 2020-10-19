<?php

namespace App\Controller\Admin;

use App\Entity\Nonprofit;
use App\Entity\Representative;
use App\Services\EmailerService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NonprofitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Nonprofit::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
                        ->setEntityLabelInSingular('Nonprofit')
                        ->setEntityLabelInPlural('Nonprofit')
                        ->setPageTitle(Crud::PAGE_EDIT, 'Edit %entity_name%')
                        ->setHelp('index', 'Activate sends notice to staff; deactivate blocks staff log in, opportunities will not be found')
                        ->setSearchFields(['id', 'orgname', 'address', 'city', 'state', 'zip', 'phone', 'website', 'ein']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                        ->disable('new', 'edit', 'delete');
    }

    public function configureFields(string $pageName): iterable
    {
        $orgname = TextField::new('orgname');
        $address = TextField::new('address');
        $city = TextField::new('city');
        $state = TextField::new('state');
        $zip = TextField::new('zip');
        $phone = TextField::new('phone');
        $website = TextField::new('website');
        $active = BooleanField::new('active')->setTemplatePath('Nonprofit/activate_button.html.twig');
        $addDate = DateTimeField::new('addDate');
        $ein = TextField::new('ein', 'EIN')->setTemplatePath('Nonprofit/ein.html.twig');
        $opportunities = AssociationField::new('opportunities');
        $reps = ArrayField::new('reps');
        $focuses = AssociationField::new('focuses');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$active, $orgname, $ein, $reps];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $orgname, $address, $city, $state, $zip, $phone, $website, $active, $addDate, $ein, $opportunities, $reps, $focuses];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$orgname, $address, $city, $state, $zip, $phone, $website, $active, $addDate, $ein, $opportunities, $reps, $focuses];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$orgname];
        }
    }
}
