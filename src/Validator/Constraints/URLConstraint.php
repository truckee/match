<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Validator/Constraints/URL.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class URLConstraint extends Constraint
{
    public $message = '{{ url }} is not a valid URL';

    public function validatedBy()
    {
        return \get_class($this) . 'Validator';
    }
}
