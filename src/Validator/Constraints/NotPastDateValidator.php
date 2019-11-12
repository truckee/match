<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Validator/Constraints/NotPastDateValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotPastDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $now = new \DateTime();

        if (!($value >  $now)) {
            $this->context->addViolation($constraint->message);

            return false;
        }

        return true;
    }
}
