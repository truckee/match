<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/Admin/VolunteerCrudController.php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\Form\Type\Field\SwitchFieldType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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

class VolunteerCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Person::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
                        ->setPageTitle(Crud::PAGE_INDEX, 'Volunteer')
//                        ->setHelp('index', 'Locking staff deactivates nonprofit and blocks staff log in. Replacing also removes current staff.')
                        ->setSearchFields(['id', 'roles', 'email', 'fname', 'sname', 'confirmationToken']);
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
//        $locked = BooleanField::new('locked');
        $locked = BooleanField::new('locked')->renderAsSwitch(false)->setTemplatePath('Admin/locked_badge.html.twig');
//        $enabled = BooleanField::new('enabled');
//        $receiveEmail = BooleanField::new('receiveEmail');
        $enabled = SwitchFieldType::new('enabled');
        $receiveEmail = SwitchFieldType::new('receiveEmail');
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

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {

        $role = serialize(["ROLE_VOLUNTEER"]);
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.roles = :role');
        $qb->setParameter('role', $role);

        return $qb;
    }

}
