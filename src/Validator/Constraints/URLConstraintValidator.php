<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Validator/Constraints/URLValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 *
 */
class URLConstraintValidator extends ConstraintValidator
{
    public function validate($url, Constraint $constraint)
    {
        if (null === $url || '' === $url) {
            return;
        }

        $regString = "/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,6}(:[0-9]{1,5})?(\/.*)?$/ix";
        if (!preg_match($regString, $url)) {
            $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ url }}', $url)
                    ->addViolation();
        }

        return true;
    }
}
