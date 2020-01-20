<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Validator/Constraints/PasswordRequirementsValidator.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordRequirementsValidator extends ConstraintValidator
{
    /**
     * @param null|string                     $value
     * @param PasswordRequirements|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

//        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
//            throw new UnexpectedTypeException($value, 'string');
//        }

        if (mb_strlen($value) < $constraint->minLength) {
            $this->context->buildViolation($constraint->tooShortMessage)
                ->setParameters(['{{length}}' => $constraint->minLength])
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($constraint->requireLetters && !preg_match('/\pL/u', $value)) {
            $this->context->buildViolation($constraint->missingLettersMessage)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($constraint->requireCaseDiff && !preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/u', $value)) {
            $this->context->buildViolation($constraint->requireCaseDiffMessage)
                ->setInvalidValue($value)
                ->addViolation();
        }

        if ($constraint->requireNumbers && !preg_match('/\pN/u', $value)) {
            $this->context->buildViolation($constraint->missingNumbersMessage)
                ->setInvalidValue($value)
                ->addViolation();
        }

//        if ($constraint->requireSpecialCharacter && !preg_match('/[^p{Ll}\p{Lu}\pL\pN]/u', $value)) {
//            $this->context->buildViolation($constraint->missingSpecialCharacterMessage)
//                ->setInvalidValue($value)
//                ->addViolation();
//        }
    }
}