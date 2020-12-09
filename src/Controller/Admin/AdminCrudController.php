<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\Form\Type\Field\SwitchFieldType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;

class AdminCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Person::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
                        ->setEntityLabelInSingular('Admin')
                        ->setEntityLabelInPlural('Admin')
                        ->setPageTitle(Crud::PAGE_EDIT, 'Edit %entity_name%')
                        ->setHelp('index', 'Mailer is designated recipient and sender for all automated email')
                        ->setDefaultSort(['sname' => 'ASC', 'fname' => 'ASC'])
                        ->setSearchFields(['id', 'roles', 'email', 'fname', 'sname', 'confirmationToken']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
                        ->disable('new', 'delete', 'edit');
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
        $mailer = SwitchFieldType::new('mailer');
        $id = IntegerField::new('id', 'ID');
        $enabled = SwitchFieldType::new('enabled');
        $fullName = TextareaField::new('fullName');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$fullName, $enabled, $email, $mailer];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$fname, $sname, $mailer];
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $role1 = serialize(["ROLE_ADMIN"]);
        $role2 = serialize(["ROLE_SUPER_ADMIN"]);
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.roles = :role1');
        $qb->orWhere('entity.roles = :role2');
        $qb->setParameter('role1', $role1);
        $qb->setParameter('role2', $role2);

        return $qb;
    }

}
