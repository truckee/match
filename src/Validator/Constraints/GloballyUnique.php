<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/insert_path_here/GloballyUnique.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class GloballyUnique extends Constraint
{
    public $message = 'Email already registered';

    public function validatedBy()
    {
        return \get_class($this) . 'Validator';
    }
}
