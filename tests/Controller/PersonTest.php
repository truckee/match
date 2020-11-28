<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/path_here/PersonTest.php

namespace App\Tests\Controller;

use App\Entity\Person;
use PHPUnit\Framework\TestCase;

class PersonTest extends TestCase
{

    public function testNewVolunteer()
    {
        $volunteer = new Person('ROLE_VOLUNTEER');
        $this->assertTrue($volunteer->hasRole('ROLE_VOLUNTEER'));
    }

}
