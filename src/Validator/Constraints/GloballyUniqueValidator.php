<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/insert_path_here/GloballyUniqueValidator.php

namespace App\Validator\Constraints;

use App\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;

class GloballyUniqueValidator extends ConstraintValidator
{
    private $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function validate($email, Constraint $constraint)
    {
        $found = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (null !== $found) {
            $this->context->buildViolation($constraint->message)
                    ->addViolation();
        }
    }
}
