<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Controller/Admin/RepresentativeCrudController.php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\Form\Type\Field\SwitchFieldType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
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

class RepresentativeCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Person::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
                        ->setPageTitle(Crud::PAGE_INDEX, 'Staff')
                        ->setHelp('index', 'Replacing removes current staff.')
                        ->setHelp('index', 'Locking staff deactivates nonprofit and blocks staff log in')
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
        $locked = SwitchFieldType::new('locked');
        $enabled = SwitchFieldType::new('enabled');
        $replacementStatus = TextField::new('replacementStatus');
        $initiated = DateField::new('initiated');
        $completed = DateField::new('completed');
        $nonprofit = AssociationField::new('nonprofit');
        $id = IntegerField::new('id', 'ID');
        $replace = TextareaField::new('replace')->setTemplatePath('Admin/replace_staff.html.twig');
        $fullName = TextareaField::new('fullName');
        $orgname = TextareaField::new('orgname');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$replace, $locked, $fullName, $email, $orgname];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $replacementStatus, $initiated, $completed, $nonprofit];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $replacementStatus, $initiated, $completed, $nonprofit];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$roles, $password, $email, $fname, $sname, $lastLogin, $confirmationToken, $tokenExpiresAt, $locked, $enabled, $replacementStatus, $initiated, $completed, $nonprofit];
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $role = serialize(["ROLE_REP"]);
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.roles = :role');
        $qb->setParameter('role', $role);

        return $qb;
    }

}
