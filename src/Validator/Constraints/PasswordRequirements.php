<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Validator/Constraints/PasswordRequirements.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PasswordRequirements extends Constraint
{
    public $tooShortMessage = 'At least {{length}} characters long.';
    public $missingLettersMessage = 'Must include at least one letter.';
    public $requireCaseDiffMessage = 'Must include both upper and lower case letters.';
    public $missingNumbersMessage = 'Must include at least one number.';

    public $minLength = 6;
    public $requireLetters = true;
    public $requireCaseDiff = true;
    public $requireNumbers = true;
}
