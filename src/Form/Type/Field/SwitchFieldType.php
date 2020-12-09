<?php

//src/Form/Type/Field/SwitchFieldType.php

namespace App\Form\Type\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Allows a switch field in EasyAdmin when entity edit is disabled
 *
 * Switch behavior is modified in App\Services\PersonService
 */
class SwitchFieldType implements FieldInterface
{

    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
                        ->setProperty($propertyName)
                        ->setLabel($label)
                        ->setTemplatePath('Admin/switch.html.twig')
                        ->setFormType(CheckboxType::class)
        ;
    }

}
